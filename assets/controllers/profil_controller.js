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
        'fileInput',
        'viewBtn'
    ];

    connect() {
        this.updateCharCount();
    }

    updateName(event) {
        const val = event.target.value.trim();
        if (this.hasPreviewNameTarget) {
            this.previewNameTarget.textContent = val ? val.split(' ')[0] + ', 22' : 'Lucas, 22';
        }
    }

    updateBio(event) {
        this.updateCharCount();
        const val = event.target.value.trim();
        if (this.hasPreviewBioTarget) {
            this.previewBioTarget.textContent = val ? `"${val}"` : `"Développeur Fullstack passionné..."`;
        }
    }

    updateCharCount() {
        if (this.hasBioInputTarget && this.hasCharCountTarget) {
            const count = this.bioInputTarget.value.length;
            this.charCountTarget.textContent = `${count} / 290`;
            if (count > 290) {
                this.charCountTarget.style.color = '#dc2c4f';
            } else {
                this.charCountTarget.style.color = '';
            }
        }
    }

    selectRhythm(event) {
        const card = event.currentTarget;
        const rhythmText = card.dataset.rhythmValue;
        
        // Mettre à jour l'input caché
        if (this.hasRhythmHiddenInputTarget) {
            this.rhythmHiddenInputTarget.value = rhythmText;
        }

        // Mettre à jour les classes actives
        this.rhythmCardTargets.forEach(el => {
            el.classList.remove('active');
            const radio = el.querySelector('.pe-rhythm-radio-icon');
            if (radio) {
                radio.src = radio.src.replace('radio_checked.svg', 'radio_unchecked.svg');
            }
        });

        card.classList.add('active');
        const activeRadio = card.querySelector('.pe-rhythm-radio-icon');
        if (activeRadio) {
            activeRadio.src = activeRadio.src.replace('radio_unchecked.svg', 'radio_checked.svg');
        }

        // Mettre à jour la prévisualisation recruteur
        if (this.hasPreviewRhythmTarget) {
            this.previewRhythmTarget.textContent = `Rythme : ${rhythmText}`;
        }
    }

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

    togglePause(event) {
        event.preventDefault();
        if (!this.hasIsPausedInputTarget) return;

        const isCurrentlyPaused = this.isPausedInputTarget.value === '1' || this.isPausedInputTarget.value === 'true';
        const nextState = !isCurrentlyPaused;

        this.isPausedInputTarget.value = nextState ? '1' : '0';

        if (this.hasPauseBtnTextTarget) {
            if (nextState) {
                this.pauseBtnTextTarget.textContent = '▶ Réactiver mon profil (Reprendre la visibilité)';
                this.pauseBtnTarget.style.backgroundColor = '#eef2ff';
                this.pauseBtnTarget.style.borderColor = '#4648d4';
                this.pauseBtnTarget.style.color = '#4648d4';
            } else {
                this.pauseBtnTextTarget.textContent = 'Mettre en pause mon profil (Alternance trouvée)';
                this.pauseBtnTarget.style.backgroundColor = '';
                this.pauseBtnTarget.style.borderColor = '';
                this.pauseBtnTarget.style.color = '';
            }
        }
    }

    triggerUpload() {
        if (this.hasFileInputTarget) {
            this.fileInputTarget.click();
        }
    }

    onPhotoSelected(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (this.hasAvatarImgTarget) {
                    this.avatarImgTarget.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    switchView(event) {
        const btn = event.currentTarget;
        this.viewBtnTargets.forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
    }
}
