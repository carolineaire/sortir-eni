document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       GESTION SORTIE PRIVÉE / LISTE DES INVITÉS
    ===================================================== */

    const checkbox = document.querySelector('#sorties_isPrivate');
    const invitesDiv = document.querySelector('#invites-list');

    if (checkbox && invitesDiv) {

        function toggleInvites() {
            invitesDiv.classList.toggle('hidden', !checkbox.checked);
        }

        // Etat au chargement
        toggleInvites();

        // Au changement
        checkbox.addEventListener('change', toggleInvites);
    }


    /* =====================================================
       GESTION VILLE → LIEUX
    ===================================================== */

    const villeSelect = document.querySelector('#sorties_noVilles');
    const lieuSelect = document.querySelector('#sorties_noLieux');

    const rueField = document.querySelector('#sorties_rue');
    const cpField = document.querySelector('#sorties_codePostal');
    const latField = document.querySelector('#sorties_latitude');
    const lonField = document.querySelector('#sorties_longitude');

    if (villeSelect && lieuSelect) {

        function resetLieuFields() {
            lieuSelect.innerHTML = '<option value="">Choisir un lieu</option>';

            if (rueField) rueField.value = '';
            if (cpField) cpField.value = '';
            if (latField) latField.value = '';
            if (lonField) lonField.value = '';
        }

        villeSelect.addEventListener('change', function () {

            resetLieuFields();

            if (!this.value) return;

            fetch('/ajax/lieux/' + this.value)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    data.forEach(lieu => {
                        const option = document.createElement('option');
                        option.value = lieu.id;
                        option.textContent = lieu.nom;
                        lieuSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des lieux :', error);
                });
        });

        lieuSelect.addEventListener('change', function () {

            if (!this.value) {
                resetLieuFields();
                return;
            }

            fetch('/ajax/lieu/' + this.value)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (rueField) rueField.value = data.rue ?? '';
                    if (cpField) cpField.value = data.cp ?? '';
                    if (latField) latField.value = data.latitude ?? '';
                    if (lonField) lonField.value = data.longitude ?? '';
                })
                .catch(error => {
                    console.error('Erreur lors du chargement du lieu :', error);
                });
        });
    }

});