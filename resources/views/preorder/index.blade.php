<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tunnel précommande tomate</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.kkiapay.mek.js"></script>
    @vite(['resources/css/preorder.css', 'resources/js/preorder.js'])
</head>
<body>
    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-grid">
                    <div>
                        <div class="eyebrow">Précommandez vos produits dès maintenant</div>
                        <h1>La Ferme de l'Amitié</h1>
                        <h3>Des produits de qualité, disponibles quand vous en avez besoin, à un prix compétitif</h3>
                        <p class="lead">Nous savons que ce qui compte le plus pour vous, c’est d’avoir des produits fiables, accessibles et livrés au bon moment.
                          C’est pourquoi nous ré-organisons notre production et notre système de précommande pour vous garantir des prix maîtrisés, 
                          une disponibilité adaptée à vos besoins et une qualité suivie avec soin.
                        </p>
                        <ul class="hero-points">
                            <li><span class="dot"></span><span>La ferme de l'amitié, votre 1er partenaire d'approvisionnement</span></li>
                            <li><span class="dot"></span><span>Des produits adaptés à vos besoins, au bon moment et au bon prix.</span></li>
                            <li><span class="dot"></span><span>Réservez plusieurs produits en toute simplicité.</span></li>
                        </ul>
                    </div>
                    <div class="video-card">
                        <div class="video-wrap">
                            <div class="video-badge">Vidéo de présentation</div>
                            <video id="heroVideo" playsinline controls>
                                <source src="{{ asset('videos/1778018576_69fa6910de3b3_VID-20260505-WA0212.mp4') }}" type="video/mp4">
                                Votre navigateur ne supporte pas la vidéo.
                            </video>
                        </div>
                        <div class="video-status">
                            <div class="status-title">étape 1</div>
                            <div class="status-line">
                                <span id="videoStatusText">Regardez la vidéo (optionnel) pour plus d'infos.</span>
                                <strong id="videoStatusFlag" style="color: var(--green);">Disponible</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="reserve-section">
                    <div class="arrow-zone">
                        <svg class="arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 4v14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M6 13l6 6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <button id="reserveBtn" class="cta">Réservez ma récolte</button>
                    <div class="subcopy" id="reserveHint">Cliquez pour réserver</div>
                </div>
            </div>
        </section>

        <section class="section">
          <div class="container">
            <div id="formShell" class="form-shell">
              <div class="form-header">
                <h2>Formulaire de réservation</h2>
                <p>Remplissez vos informations, indiquez vos besoins de livraison, puis soumettez. Nous enregistrons votre demande immédiatement. Après cela, vous pourrez soit laisser la réservation telle quelle, soit la renforcer avec un paiement facultatif de 100 FCFA par kilo réservé.</p>
              </div>

              <div class="card" style="padding:24px;">
                <form id="preorderForm" action="{{ route('preorder.store') }}" method="POST">
                    @csrf
                  <div class="grid">
                    <div class="field">
                      <label for="firstName">Prénom</label>
                      <input id="firstName" name="firstName" required placeholder="Ex. Serge" />
                    </div>
                    <div class="field">
                      <label for="lastName">Nom</label>
                      <input id="lastName" name="lastName" required placeholder="Ex. Afouda" />
                    </div>
                    <div class="field">
                      <label for="email">Adresse email</label>
                      <input id="email" name="email" type="email" required placeholder="vous@email.com" />
                    </div>
                    <div class="field">
                      <label for="phone">Téléphone</label>
                      <input id="phone" name="phone" placeholder="229xxxxxxxx" />
                    </div>
                    <div class="field full">
                      <label for="projectNote">Message ou précision</label>
                      <textarea id="projectNote" name="projectNote" rows="4" placeholder="Ex. Je veux une première livraison pour la maison, puis une autre pour ma boutique."></textarea>
                    </div>
                  </div>

                  <div class="orders-box">
                    <div class="orders-top">
                      <div>
                        <h3>Détails des livraisons</h3>
                        <div class="helper">Ajoutez autant de lignes que nécessaire. Chaque ligne peut avoir son propre produit, sa quantité, sa date de livraison et son adresse.</div>
                      </div>
                      <button type="button" class="cta secondary" id="addRowBtn">Ajouter une livraison</button>
                    </div>

                    <div class="row-list" id="rowsContainer"></div>

                    <div class="summary-bar">
                      <div class="summary-item">
                        <div class="summary-label">Quantité totale</div>
                        <div class="summary-value" id="totalKg">0 kg</div>
                      </div>
                      <div class="summary-item">
                        <div class="summary-label">Tomate fruit</div>
                        <div class="summary-value" id="fruitKg">0 kg</div>
                      </div>
                      <div class="summary-item">
                        <div class="summary-label">Tomate purée</div>
                        <div class="summary-value" id="pureeKg">0 kg</div>
                      </div>
                    </div>

                    <div class="notice">Le prix final sera communiqué à la récolte. Nous l'organisons pour qu'il reste maîtrisé et inférieur ou très compétitif face au prix du marché de contre-saison, sans vous promettre un chiffre irréaliste à l'avance.</div>
                  </div>

                  <div class="submit-line">
                    <p>En soumettant, vous nous permettez d'enregistrer votre demande et de préparer l'organisation des volumes. Le paiement n'est pas obligatoire à cette étape.</p>
                    <button type="submit" class="cta">Enregistrer ma réservation</button>
                  </div>
                </form>
              </div>

              <div id="afterSubmit" class="after-submit">
                <div class="success-head">
                  <div class="success-icon">✓</div>
                  <div>
                    <h3>Réservation enregistrée</h3>
                    <p>Vos informations ont bien été sauvegardées. Vous pouvez maintenant choisir de renforcer votre priorité avec un paiement facultatif de 100 FCFA par kilo réservé, ou simplement garder votre réservation sans payer pour le moment.</p>
                  </div>
                </div>

                <div class="pay-panel">
                  <div class="pay-card">
                    <h4>Option 1 : confirmer plus fortement avec paiement</h4>
                    <p>Le paiement est facultatif mais recommandé. Il montre un engagement plus fort, nous aide à mieux planifier la production et pourra être pris en compte dans l'ordre de sécurisation des réservations.</p>
                    <div class="pay-amount" id="optionalAmount">0 FCFA</div>
                    <div class="pay-note">Base de calcul : 100 FCFA × quantité totale réservée. Ce montant sert d'acompte de sécurisation. Il peut être remboursé si l'objectif n'est pas atteint ou en cas de non-livraison selon vos conditions commerciales.</div>
                    <div class="pay-actions">
                      <button id="payNowBtn" type="button" class="cta">Payer maintenant</button>
                      <button id="skipPayBtn" type="button" class="cta secondary">Continuer sans payer</button>
                    </div>
                    <div class="mail-note">Après paiement, vous pourrez envoyer automatiquement un email de confirmation et votre accord de précommande depuis votre backend ou votre outil d'emailing.</div>
                  </div>

                  <div class="pay-card">
                    <h4>Option 2 : garder la réservation sans paiement</h4>
                    <p>Cette option laisse votre demande enregistrée. Vous pourrez être recontacté pour confirmer plus tard, ajuster les volumes ou payer ensuite.</p>
                    <ul class="tiny-list">
                      <li>Vos informations restent enregistrées même si vous ignorez le paiement.</li>
                      <li>Vous gardez une porte d'entrée simple pour convertir un maximum de prospects.</li>
                      <li>Le paiement facultatif peut aussi être relancé plus tard par WhatsApp, email ou appel.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
    </main>
    <script>
        window.APP_CONFIG = {
            kkiapayPublicKey: "{{ config('services.kkiapay.public_key') }}",
            kkiapayCallback: "{{ config('services.kkiapay.callback') }}"
        };
    </script>

    <script src="https://cdn.kkiapay.me/k.js"></script>

</body>
</html>



