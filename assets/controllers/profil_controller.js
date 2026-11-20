import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'fullNameInput',
        'previewName',
        'bioInput',
        'charCount',
        'previewBio',
        'rhythmCard',
        'rhythmHiddenInput',
        'previewRhythm',
        'durationPill',
        'durationHiddenInput',
        'previewDuration',
        'isPausedInput',
        'pauseBtn',
        'pauseBtnText',
        'avatarImg',
        'liveCardPhoto',
        'fileInput',       // input[type=file] photo (rendu par Symfony FileType)
        'cvFileInput',     // input[type=file] CV (rendu par Symfony FileType)
        'cvFilename',      // <p> affichant le nom du CV
        'viewBtn'
    ];

    connect() {
        this.updateCharCount();
        // Initialiser l'état visuel du bouton pause depuis la valeur cachée
        this._syncPauseBtn();
    }

    // ── Nom ──────────────────────────────────────────────────────────────────
    updateName(event) {
        const val = event.target.value.trim();
        if (this.hasPreviewNameTarget) {
            this.previewNameTarget.textContent = val ? val.split(' ')[0] : '';
        }
    }

    // ── Bio ───────────────────────────────────────────────────────────────────
    updateBio(event) {
        this.updateCharCount();
        const val = event.target.value.trim();
        if (this.hasPreviewBioTarget) {
            this.previewBioTarget.textContent = val ? `"${val}"` : '';
        }
    }

    updateCharCount() {
        if (this.hasBioInputTarget && this.hasCharCountTarget) {
            const count = this.bioInputTarget.value.length;
            this.charCountTarget.textContent = `${count} / 290`;
            this.charCountTarget.style.color = count > 290 ? '#dc2c4f' : '';
        }
    }

    // ── Rythme ────────────────────────────────────────────────────────────────
    selectRhythm(event) {
        const card = event.currentTarget;
        const rhythmText = card.dataset.rhythmValue;

        if (this.hasRhythmHiddenInputTarget) {
            this.rhythmHiddenInputTarget.value = rhythmText;
        }

        this.rhythmCardTargets.forEach(el => {
            el.classList.remove('active');
            const radio = el.querySelector('.pe-rhythm-radio-icon');
            if (radio) radio.src = radio.src.replace('radio_checked.svg', 'radio_unchecked.svg');
        });

        card.classList.add('active');
        const activeRadio = card.querySelector('.pe-rhythm-radio-icon');
        if (activeRadio) activeRadio.src = activeRadio.src.replace('radio_unchecked.svg', 'radio_checked.svg');

        if (this.hasPreviewRhythmTarget) {
            this.previewRhythmTarget.textContent = `Rythme : ${rhythmText}`;
        }
    }

    // ── Durée ─────────────────────────────────────────────────────────────────
    selectDuration(event) {
        const btn = event.currentTarget;
        const duration = btn.dataset.durationValue;

        if (this.hasDurationHiddenInputTarget) {
            this.durationHiddenInputTarget.value = duration;
        }

        this.durationPillTargets.forEach(el => el.classList.remove('active'));
        btn.classList.add('active');

        if (this.hasPreviewDurationTarget) {
            this.previewDurationTarget.textContent = duration;
        }
    }

    // ── Pause ─────────────────────────────────────────────────────────────────
    togglePause(event) {
        event.preventDefault();
        if (!this.hasIsPausedInputTarget) return;

        const isCurrentlyPaused = this.isPausedInputTarget.value === '1' || this.isPausedInputTarget.value === 'true';
        const nextState = !isCurrentlyPaused;
        this.isPausedInputTarget.value = nextState ? '1' : '0';
        this._syncPauseBtn(nextState);
    }

    _syncPauseBtn(forcedState = null) {
        if (!this.hasIsPausedInputTarget) return;
        const isPaused = forcedState !== null
            ? forcedState
            : (this.isPausedInputTarget.value === '1' || this.isPausedInputTarget.value === 'true');

        if (this.hasPauseBtnTextTarget) {
            this.pauseBtnTextTarget.textContent = isPaused
                ? '▶ Réactiver mon profil (Reprendre la visibilité)'
                : 'Mettre en pause mon profil (Alternance trouvée)';
        }
        if (this.hasPauseBtnTarget) {
            if (isPaused) {
                this.pauseBtnTarget.style.backgroundColor = '#eef2ff';
                this.pauseBtnTarget.style.borderColor = '#4648d4';
                this.pauseBtnTarget.style.color = '#4648d4';
            } else {
                this.pauseBtnTarget.style.backgroundColor = '';
                this.pauseBtnTarget.style.borderColor = '';
                this.pauseBtnTarget.style.color = '';
            }
        }
    }

    // ── Upload photo ──────────────────────────────────────────────────────────
    triggerPhotoUpload() {
        if (this.hasFileInputTarget) {
            this.fileInputTarget.click();
        }
    }

    // Alias pour compatibilité avec les anciens boutons data-action="click->profil#triggerUpload"
    triggerUpload() {
        this.triggerPhotoUpload();
    }

    onPhotoSelected(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            if (this.hasAvatarImgTarget) {
                this.avatarImgTarget.src = e.target.result;
            }
            if (this.hasLiveCardPhotoTarget) {
                this.liveCardPhotoTarget.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }

    // ── Upload CV ─────────────────────────────────────────────────────────────
    triggerCvUpload() {
        if (this.hasCvFileInputTarget) {
            this.cvFileInputTarget.click();
        }
    }

    onCvSelected(event) {
        const file = event.target.files[0];
        if (!file) return;
        if (this.hasCvFilenameTarget) {
            this.cvFilenameTarget.textContent = file.name;
        }
    }

    // ── Vue switcher ──────────────────────────────────────────────────────────
    switchView(event) {
        const btn = event.currentTarget;
        this.viewBtnTargets.forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
    }
}
