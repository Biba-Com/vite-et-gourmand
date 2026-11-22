<?php
// Integration test for the "modify order" flow (controller -> service -> repository).
// Run against a database loaded with create.sql + fixtures.sql:
//   DB_USER=... DB_PASS=... php tests/test_update_order.php
session_start();
require __DIR__ . '/../src/config/database.php';
require __DIR__ . '/../src/controllers/CommandeController.php';

$pdo = getDbConnection();
$ctl = new CommandeController($pdo);
$svc = new CommandeService();
$repo = new CommandeRepository($pdo);
$ok = 0; $ko = 0;
function check($label, $cond) { global $ok, $ko; echo ($cond ? "PASS " : "FAIL ") . $label . "\n"; $cond ? $ok++ : $ko++; }

// 0) Place a new pending order for user 6 (menu 4 "Menu Festif", 38 €, min 15)
$panier = [['id_menu' => 4, 'prix_unitaire' => 38.0, 'nb_personnes' => 20, 'nb_personnes_min' => 15,
            'sous_total_brut' => 760.0, 'remise' => 76.0, 'sous_total' => 684.0]];
$date = (new DateTime('+10 days'))->format('Y-m-d');
$id = $svc->placeOrder($repo, 6, ['adresse' => '1 rue Test', 'code_postal' => '33000', 'ville' => 'Bordeaux',
                                   'date_evenement' => $date, 'heure_evenement' => '19:00'], $panier);
check("order #$id created as pending", $repo->findByIdForUser($id, 6)['statut'] === 'pending');

$post = ['nb_personnes' => 25, 'adresse_livraison' => '2 rue Test', 'code_postal' => '33000',
         'ville' => 'Bordeaux', 'date_evenement' => $date, 'heure_evenement' => '20:00'];

// 1) Valid update in Bordeaux: 25 × 38 = 950, discount 10 % (25 ≥ 15 + 5), no delivery fee
$r = $ctl->updateOrder($id, $post, 6);
$o = $repo->findByIdForUser($id, 6);
check('update accepted', $r['success'] === true);
check('sous_total 950.00', $o['sous_total'] === '950.00');
check('remise 95.00', $o['montant_remise'] === '95.00');
check('frais 0.00 (Bordeaux)', $o['frais_livraison'] === '0.00');
check('total 855.00', $o['total'] === '855.00');
check('nb_personnes 25', (int) $o['nb_personnes'] === 25);
$line = $pdo->query("SELECT quantite, sous_total FROM ligne_commande WHERE id_commande = $id")->fetch();
check('line quantity 25 and line subtotal 950.00', (int) $line['quantite'] === 25 && $line['sous_total'] === '950.00');

// 2) No discount below min + 5 (18 < 20), delivery outside Bordeaux: Pessac 6 km → 5 + 6 × 0.59 = 8.54
$r = $ctl->updateOrder($id, array_merge($post, ['nb_personnes' => 18, 'ville' => 'Pessac']), 6);
$o = $repo->findByIdForUser($id, 6);
check('update 18 guests in Pessac accepted', $r['success'] === true);
check('remise 0.00 (18 < 20)', $o['montant_remise'] === '0.00');
check('frais 8.54 (Pessac, 6 km)', $o['frais_livraison'] === '8.54');
check('total 692.54', $o['total'] === '692.54');

// 3) Below the menu minimum → rejected, nothing written
$r = $ctl->updateOrder($id, array_merge($post, ['nb_personnes' => 10]), 6);
check('10 guests rejected (menu minimum 15)', $r['success'] === false && str_contains($r['errors'][0] ?? '', '15'));
check('order unchanged after rejection', (int) $repo->findByIdForUser($id, 6)['nb_personnes'] === 18);

// 4) Date less than 48 h ahead → rejected
$r = $ctl->updateOrder($id, array_merge($post, ['date_evenement' => date('Y-m-d')]), 6);
check('date within 48 h rejected', $r['success'] === false);

// 5) Someone else's order → refused
check('order of another user not editable', $ctl->getEditableOrder($id, 4) === null);
$r = $ctl->updateOrder($id, $post, 4);
check('update by another user refused', $r['success'] === false);

// 6) Non-pending order (#1 is completed) → refused
check('completed order not editable', $ctl->getEditableOrder(1, 6) === null);
check('update of completed order refused', $ctl->updateOrder(1, $post, 6)['success'] === false);

// 7) Repository guard: direct update of a non-pending order writes nothing
check('repository refuses non-pending order', $repo->update(1, 6, ['dateEvenement' => "$date 12:00:00", 'adresseLivraison' => 'x',
    'codePostalLivraison' => '33000', 'villeLivraison' => 'Bordeaux', 'distanceKm' => 0, 'nbPersonnes' => 99,
    'sousTotal' => 1, 'montantRemise' => 0, 'fraisLivraison' => 0, 'total' => 1]) === false);
check('completed order untouched', (int) $repo->findByIdForUser(1, 6)['nb_personnes'] === 20);

// 8) History: creation + 2 successful modifications
$h = (int) $pdo->query("SELECT COUNT(*) FROM historique_statut WHERE id_commande = $id")->fetchColumn();
check("history rows = 3 (got $h)", $h === 3);

// 9) Editable order exposes the menu minimum for the view
check('getEditableOrder returns nb_personnes_min 15', (int) $ctl->getEditableOrder($id, 6)['nb_personnes_min'] === 15);

echo "\n$ok passed, $ko failed\n";
exit($ko ? 1 : 0);
