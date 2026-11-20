import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'companyNameInput',
        'previewName',
        'sectorInput',
        'previewSector',
        'bioInput',
        'charCount',
        'previewBio',
        'countPill',
        'countHiddenInput',
        'previewCount',
        'isPausedInput',
        'pauseBtn',
        'pauseBtnText',
        'avatarImg',
        'liveCardPhoto',
        'fileInput',
        'viewBtn'
    ];

    connect() {
        this.updateCharCount();
        this._syncPauseBtn();
    }

    // ── Nom de l'entreprise ───────────────────────────────────────────────────
    updateName(event) {
        const val = event.target.value.trim();
        if (this.hasPreviewNameTarget) {
            this.previewNameTarget.textContent = val || 'Mon Entreprise';
        }
    }

    // ── Secteur d'activité ────────────────────────────────────────────────────
    updateSector(event) {
        const val = event.target.value.trim();
        if (this.hasPreviewSectorTarget) {
            this.previewSectorTarget.textContent = val || 'Secteur d\'activité';
        }
    }

    // ── Présentation / Pitch ──────────────────────────────────────────────────
    updateBio(event) {
        this.updateCharCount();
        const val = event.target.value.trim();
        if (this.hasPreviewBioTarget) {
            this.previewBioTarget.textContent = val ? `"${val}"` : '""';
        }
    }

    updateCharCount() {
        if (this.hasBioInputTarget && this.hasCharCountTarget) {
            const count = this.bioInputTarget.value.length;
            this.charCountTarget.textContent = `${count} / 500`;
            this.charCountTarget.style.color = count > 500 ? '#dc2c4f' : '';
        }
    }

    // ── Taille de l'entreprise (Selecteur de badges) ──────────────────────────
    selectCount(event) {
        const pill = event.currentTarget;
        const countVal = pill.dataset.countValue;
        const countLabel = pill.dataset.countLabel || countVal;

        if (this.hasCountHiddenInputTarget) {
            this.countHiddenInputTarget.value = countVal;
        }

        this.countPillTargets.forEach(el => {
            el.classList.remove('active');
            const radio = el.querySelector('.pe-rhythm-radio-icon');
            if (radio) radio.src = radio.src.replace('radio_checked.svg', 'radio_unchecked.svg');
        });

        pill.classList.add('active');
        const activeRadio = pill.querySelector('.pe-rhythm-radio-icon');
        if (activeRadio) activeRadio.src = activeRadio.src.replace('radio_unchecked.svg', 'radio_checked.svg');

        if (this.hasPreviewCountTarget) {
            this.previewCountTarget.textContent = countLabel;
        }
    }

    // ── Upload Logo ───────────────────────────────────────────────────────────
    triggerLogoUpload(event) {
        event.preventDefault();
        if (this.hasFileInputTarget) {
            this.fileInputTarget.click();
        }
    }

    onLogoSelected(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const url = e.target.result;
            if (this.hasAvatarImgTarget) {
                this.avatarImgTarget.src = url;
            }
            if (this.hasLiveCardPhotoTarget) {
                this.liveCardPhotoTarget.src = url;
            }
        };
        reader.readAsDataURL(file);
    }

    // ── Pause / Visibilité ───────────────────────────────────────────────────
    togglePause(event) {
        event.preventDefault();
        if (!this.hasIsPausedInputTarget) return;

        const currentlyPaused = this.isPausedInputTarget.value === '1' || this.isPausedInputTarget.value === 'true';
        const nextState = !currentlyPaused;

        this.isPausedInputTarget.value = nextState ? '1' : '0';
        this._syncPauseBtn();
    }

    _syncPauseBtn() {
        if (!this.hasIsPausedInputTarget || !this.hasPauseBtnTarget) return;

        const isPaused = this.isPausedInputTarget.value === '1' || this.isPausedInputTarget.value === 'true';

        if (this.hasPauseBtnTextTarget) {
            this.pauseBtnTextTarget.textContent = isPaused
                ? '▶ Réactiver mon profil entreprise (Reprendre la visibilité)'
                : 'Mettre en pause mon profil (Recrutements clôturés)';
        }

        if (isPaused) {
            this.pauseBtnTarget.classList.add('pe-btn-pause-active');
        } else {
            this.pauseBtnTarget.classList.remove('pe-btn-pause-active');
        }
    }

    // ── Basculer la vue (Édition vs Aperçu) ────────────────────────────────────
    switchView(event) {
        const clicked = event.currentTarget;
        this.viewBtnTargets.forEach(b => b.classList.remove('active'));
        clicked.classList.add('active');
    }
}
