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
        <div class="hero-top-actions">
          <a href="https://wa.me/{{ config('services.whatsapp.phone') }}" target="_blank" class="whatsapp-btn">
            <svg class="w-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M11.99 2C6.47 2 2 6.48 2 12c0 1.84.5 3.6 1.4 5.15L2 22l4.98-1.39A9.95 9.95 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 11.99 2zM12 20.17c-1.57 0-3.1-.41-4.44-1.2l-.32-.19-2.93.82.83-2.85-.21-.34A8.16 8.16 0 0 1 3.83 12c0-4.51 3.67-8.17 8.16-8.17 4.5 0 8.16 3.66 8.16 8.17s-3.66 8.17-8.15 8.17zm4.51-6.14c-.25-.12-1.47-.73-1.69-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.15.17-.3.19-.55.07-.25-.12-1.05-.39-2-1.23-.74-.65-1.23-1.46-1.38-1.71-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.44.13-.15.17-.25.25-.42.08-.17.04-.32-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.48-.4-.42-.55-.42h-.47c-.17 0-.45.06-.68.32-.23.25-.88.86-.88 2.1s.9 2.43 1.03 2.6c.13.17 1.77 2.7 4.29 3.78.6.26 1.07.41 1.43.53.58.18 1.11.16 1.53.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.28z"/>
            </svg>
            <span>Contact WhatsApp</span>
          </a>
        </div>
        <div class="hero-grid">
          <div>
            <div class="eyebrow">Précommande pilote Tomate fruit/purée</div>
            <h1>Réservez votre récolte avant la contre-saison</h1>

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

            <p class="lead">Nous transformons et organisons la production en saison normale pour mieux maîtriser les coûts, puis nous livrons en contre-saison avec l'objectif de garder les prix en dessous du marché au moment de la récolte et de la distribution.</p>
            <ul class="hero-points">
              <li><span class="dot"></span><span>Choisissez entre tomate fruit et tomate en purée, selon vos besoins réels de consommation ou de revente.</span></li>
              <li><span class="dot"></span><span>Le prix final n'est pas figé aujourd'hui, il sera fixé à la récolte, avec un engagement clair de rester maîtrisé et compétitif par rapport au marché local.</span></li>
              <li><span class="dot"></span><span>Vous pouvez planifier plusieurs livraisons, avec des quantités, dates et adresses différentes sur un seul formulaire.</span></li>
            </ul>
          </div>
        </div>
        <div class="reserve-section">
          <div class="arrow-zone">
            <svg class="arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 4v14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
              <path d="M6 13l6 6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
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
            <p>Remplissez vos informations, indiquez vos besoins de livraison, puis soumettez. Nous enregistrons votre demande immédiatement. Après cela, vous pourrez soit laisser la réservation telle quelle, soit la renforcer avec un acompte facultatif (minimum 100 FCFA / kg) pour bénéficier d'un traitement prioritaire. Garantie : si la commande ne peut pas être honorée, vous êtes notifié dans les 48h et intégralement remboursé.</p>
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
                  <div class="summary-item">
                    <div class="summary-label">Lapin</div>
                    <div class="summary-value" id="lapinKg">0 kg</div>
                  </div>
                  <div class="summary-item">
                    <div class="summary-label">Poulet Goliath</div>
                    <div class="summary-value" id="pouletGoliathKg">0 kg</div>
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
                <p>Vos informations ont bien été sauvegardées. Vous pouvez maintenant choisir de renforcer votre priorité avec un acompte facultatif (minimum 100 FCFA par kilo réservé), ou simplement garder votre réservation sans payer pour le moment.</p>
              </div>
            </div>

            <div class="pay-panel">
              <div class="pay-card">
                <h4>Option 1 : Confirmer avec un acompte prioritaire (Recommandé)</h4>
                <p>En payant un acompte (minimum 100 FCFA / kg réservé), <strong>votre précommande sera traitée en priorité absolue</strong> lors de la récolte et des expéditions.</p>
                <div class="pay-amount" id="optionalAmount">0 FCFA</div>
                <div class="pay-note">
                  <strong>Garantie de remboursement 100% :</strong> Si votre commande ne peut pas être honorée pour une quelconque raison, <strong>vous serez notifié dans les 48h et votre argent vous sera remboursé en intégralité.</strong>
                </div>
                <div class="pay-actions">
                  <button id="payNowBtn" type="button" class="cta">Payer mon acompte</button>
                  <button id="skipPayBtn" type="button" class="cta secondary">Continuer sans payer</button>
                </div>
                <div class="mail-note">Après paiement, vous recevrez automatiquement une confirmation par email et WhatsApp.</div>
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
      paydunyaUrl: "{{ route('preorder.paydunya') }}"
    };
  </script>

</body>

</html>