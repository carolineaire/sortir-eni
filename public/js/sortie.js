const page = document.getElementById('sortie-page');
const currentUserId = parseInt(page.dataset.userId);

        // Date du jour
        document.getElementById('date-du-jour')
            .textContent = new Date().toLocaleDateString('fr-FR');

        // Filtres
        const selectVille = document.getElementById('site');
        const searchInput = document.getElementById('search');
        const dateStartInput = document.getElementById('dateStart');
        const dateEndInput = document.getElementById('dateEnd');
        const passeesCheckbox = document.getElementById('passees');
        const orgaCheckbox = document.getElementById('orga');
        const inscritCheckbox = document.getElementById('inscrit');
        const nonInscritCheckbox = document.getElementById('nonInscrit');

        const rows = document.querySelectorAll('table tbody tr');

        function applyFilters() {

            const selectedVille = selectVille.value;
            const searchValue = searchInput.value.toLowerCase();
            const startDate = dateStartInput.value ? new Date(dateStartInput.value) : null;
            const endDate = dateEndInput.value ? new Date(dateEndInput.value) : null;
            const showPassees = passeesCheckbox.checked;
            const showOrga = orgaCheckbox.checked;
            const showInscrit = inscritCheckbox.checked;
            const showNonInscrit = nonInscritCheckbox.checked;

            const today = new Date();
            today.setHours(0,0,0,0);

            rows.forEach(row => {

                const titre = row.cells[0].textContent.toLowerCase();
                const ville = row.cells[6].textContent;
                const dateText = row.cells[2].textContent.trim();
                const [day, month, year] = dateText.split('/');
                const rowDate = new Date(`${year}-${month}-${day}`);
                rowDate.setHours(0,0,0,0);

                const orgaId = row.dataset.organisateur;
                const isInscrit = row.dataset.inscrit === "1";

                let show = true;

                // Filtre ville
                if (selectedVille && ville !== selectedVille) show = false;

                // Filtre recherche
                if (searchValue && !titre.includes(searchValue)) show = false;

                // Filtre intervalle de date
                if (startDate && rowDate < startDate) show = false;
                if (endDate && rowDate > endDate) show = false;

                // Filtre sorties passées
                if (showPassees && rowDate >= today) show = false;

                // Filtre organisateur
                if (showOrga && parseInt(orgaId) !== currentUserId) show = false;

                // Filtre inscrit / non inscrit
                if (showInscrit && !isInscrit) show = false;
                if (showNonInscrit && isInscrit) show = false;

                // Si les deux sont cochés → on annule le filtre
                if (showInscrit && showNonInscrit) show = true;

                row.style.display = show ? '' : 'none';
            });
        }

        // Événements
        selectVille.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);
        dateStartInput.addEventListener('change', applyFilters);
        dateEndInput.addEventListener('change', applyFilters);
        passeesCheckbox.addEventListener('change', applyFilters);
        orgaCheckbox.addEventListener('change', applyFilters);
        inscritCheckbox.addEventListener('change', applyFilters);
        nonInscritCheckbox.addEventListener('change', applyFilters);

        // Reset filtres
        const resetBtn = document.getElementById('resetFilters');

        resetBtn.addEventListener('click', (e) => {
            e.preventDefault();

            selectVille.value = '';
            searchInput.value = '';
            dateStartInput.value = '';
            dateEndInput.value = '';
            passeesCheckbox.checked = false;
            orgaCheckbox.checked = false;
            inscritCheckbox.checked = false;
            nonInscritCheckbox.checked = false;

            rows.forEach(row => row.style.display = '');

            applyFilters();
        });

        // Popup désinscription
        document.querySelectorAll('.btn-desinscription').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                if (confirm("Voulez-vous vraiment vous désinscrire ?")) {
                    window.location.href = this.href;
                }
            });
        });