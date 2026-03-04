const checkbox = document.querySelector('#sorties_isPrivate');
const invitesDiv = document.querySelector('#invites-list');

function toggleInvites() {
    if (checkbox.checked) {
        invitesDiv.style.display = 'block';   // privée → montrer
    } else {
        invitesDiv.style.display = 'none';    // publique → cacher
    }
}

toggleInvites();
checkbox.addEventListener('change', toggleInvites);


const villeSelect = document.querySelector('#sorties_noVilles');
const lieuSelect = document.querySelector('#sorties_noLieux');

const rueField = document.querySelector('#sorties_rue');
const cpField = document.querySelector('#sorties_codePostal');
const latField = document.querySelector('#sorties_latitude');
const lonField = document.querySelector('#sorties_longitude');

function resetLieuFields() {
    lieuSelect.innerHTML = '<option value="">Choisir un lieu</option>';
    rueField.value = '';
    cpField.value = '';
    latField.value = '';
    lonField.value = '';
}

villeSelect.addEventListener('change', function () {
    resetLieuFields();
    if (!this.value) return;

    fetch('/ajax/lieux/' + this.value)
        .then(r => r.json())
        .then(data => {
            data.forEach(lieu => {
                lieuSelect.innerHTML += `<option value="${lieu.id}">${lieu.nom}</option>`;
            });
        });
});

lieuSelect.addEventListener('change', function () {
    if (!this.value) {
        resetLieuFields();
        return;
    }

    fetch('/ajax/lieu/' + this.value)
        .then(r => r.json())
        .then(data => {
            rueField.value = data.rue ?? '';
            cpField.value = data.cp ?? '';
            latField.value = data.latitude ?? '';
            lonField.value = data.longitude ?? '';
        });
});