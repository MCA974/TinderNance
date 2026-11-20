// assets/controllers/recruteur_controller.js
// Stimulus controller — Gestion Recruteur & Modal Nouvelle Annonce

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'companyName', 'sector', 'bio', 'employeeCount',
        'previewName', 'previewSector', 'previewBio', 'previewCount',
        'modal', 'modalOverlay',
        'passBtn', 'validateBtn', 'cardWrapper',
        'rhythmBtn', 'durationBtn', 'remoteBtn',
        'tagInput', 'tagList',
        'salarySlider', 'salaryDisplay',
    ];

    connect() {
        this._syncPreviews();
    }

    // ── Live previews ─────────────────────────────────────────────────────────

    previewName(event) {
        if (this.hasPreviewNameTarget) {
            this.previewNameTarget.textContent = event.target.value || 'Nom de l\'entreprise';
        }
    }

    updateBio(event) {
        if (this.hasPreviewBioTarget) {
            this.previewBioTarget.textContent = event.target.value;
        }
    }

    updateCount(event) {
        if (this.hasPreviewCountTarget) {
            this.previewCountTarget.textContent = event.target.options[event.target.selectedIndex].text;
        }
    }

    _syncPreviews() {
        if (this.hasCompanyNameTarget && this.hasPreviewNameTarget) {
            this.previewNameTarget.textContent = this.companyNameTarget.value || 'Nom de l\'entreprise';
        }
        if (this.hasBioTarget && this.hasPreviewBioTarget) {
            this.previewBioTarget.textContent = this.bioTarget.value;
        }
    }

    // ── Swipe actions ─────────────────────────────────────────────────────────

    pass(event) {
        event.preventDefault();
        const card = this.hasCardWrapperTarget ? this.cardWrapperTarget : null;
        if (card) {
            card.classList.add('rp-swipe-left');
            setTimeout(() => card.classList.remove('rp-swipe-left'), 600);
        }
        this._showToast('Candidat passé ➜', 'rp-toast-pass');
    }

    validate(event) {
        event.preventDefault();
        const card = this.hasCardWrapperTarget ? this.cardWrapperTarget : null;
        if (card) {
            card.classList.add('rp-swipe-right');
            setTimeout(() => card.classList.remove('rp-swipe-right'), 600);
        }
        this._showToast('✅ Match en attente de confirmation !', 'rp-toast-match');
    }

    // ── Modal Nouvelle Annonce ────────────────────────────────────────────────

    openModal(event) {
        event.preventDefault();
        if (this.hasModalTarget) {
            this.modalTarget.classList.add('rp-modal--open');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(event) {
        event.preventDefault();
        if (this.hasModalTarget) {
            this.modalTarget.classList.remove('rp-modal--open');
            document.body.style.overflow = '';
        }
    }

    closeModalOnOverlay(event) {
        if (event.target === event.currentTarget) {
            this.closeModal(event);
        }
    }

    // ── Toggle rhythm / duration / remote pills ───────────────────────────────

    toggleRhythm(event) {
        this._togglePillGroup(this.rhythmBtnTargets, event.currentTarget);
    }

    toggleDuration(event) {
        this._togglePillGroup(this.durationBtnTargets, event.currentTarget);
    }

    toggleRemote(event) {
        this._togglePillGroup(this.remoteBtnTargets, event.currentTarget);
    }

    _togglePillGroup(group, active) {
        group.forEach(btn => {
            btn.classList.toggle('rp-pill--active', btn === active);
            btn.classList.toggle('rp-pill--inactive', btn !== active);
        });
    }

    // ── Tags (stack technique) ────────────────────────────────────────────────

    addTag(event) {
        if (event.key !== 'Enter' && event.key !== ',') return;
        event.preventDefault();
        const value = (event.target.value || '').trim();
        if (!value || !this.hasTagListTarget) return;

        const tag = document.createElement('div');
        tag.className = 'rp-tag rp-tag--primary';
        tag.innerHTML = `<span>${value}</span><button type="button" data-action="click->recruteur#removeTag" class="rp-tag-remove">×</button>`;
        this.tagListTarget.appendChild(tag);
        event.target.value = '';
    }

    removeTag(event) {
        event.currentTarget.closest('.rp-tag')?.remove();
    }

    // ── Salary slider ─────────────────────────────────────────────────────────

    updateSalary(event) {
        if (this.hasSalaryDisplayTarget) {
            this.salaryDisplayTarget.textContent = `${Number(event.target.value).toLocaleString('fr-FR')} €/mois`;
        }
    }

    // ── Toast helper ─────────────────────────────────────────────────────────

    _showToast(message, cssClass) {
        const toast = document.createElement('div');
        toast.className = `rp-toast ${cssClass}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('rp-toast--show'));
        setTimeout(() => {
            toast.classList.remove('rp-toast--show');
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }
}
