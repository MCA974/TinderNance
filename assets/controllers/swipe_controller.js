import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'card',
        'statusCounter',
        'progressFill',
        'emptyState',
        'deckWrapper',
        'matchModal',
        'matchCompanyName',
        'matchCompanyLogo',
        'matchOfferTitle',
        'undoBtn',
        'ghostCard1',
        'ghostCard2',
    ];

    static values = {
        swipeUrl: String,
        rewindUrl: String,
    };

    connect() {
        this.currentIndex = 0;
        this.history = [];
        this.updateDeckState();
        this.initDragEvents();
    }

    // ── Swipes via boutons ───────────────────────────────────────────────────

    pass(event) {
        if (event) event.preventDefault();
        this.performSwipe('DISLIKE');
    }

    like(event) {
        if (event) event.preventDefault();
        this.performSwipe('LIKE');
    }

    superLike(event) {
        if (event) event.preventDefault();
        this.performSwipe('SUPER_LIKE');
    }

    // ── Exécution de l'animation et de l'appel API ───────────────────────────

    performSwipe(action) {
        const activeCard = this.currentCard;
        if (!activeCard) return;

        const offerId = activeCard.dataset.offerId;
        if (!offerId) return;

        // Animation de sortie
        activeCard.classList.add('animating-out');
        if (action === 'DISLIKE') {
            activeCard.classList.add('swipe-left');
        } else if (action === 'SUPER_LIKE') {
            activeCard.classList.add('swipe-up');
        } else {
            activeCard.classList.add('swipe-right');
        }

        // Enregistrement dans l'historique local pour l'animation undo
        this.history.push({
            card: activeCard,
            offerId: offerId,
            action: action,
        });

        // Appel API en arrière-plan
        const url = this.swipeUrlValue.replace('__ID__', offerId);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ action: action }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.isMatch) {
                this.showMatchModal(data);
            }
        })
        .catch(err => console.error('Erreur lors du swipe:', err));

        // Passage à la carte suivante après la fin de l'animation
        setTimeout(() => {
            activeCard.style.display = 'none';
            this.currentIndex++;
            this.updateDeckState();
        }, 300);
    }

    // ── Annuler le dernier swipe (Undo / Rewind) ───────────────────────────────

    undo(event) {
        if (event) event.preventDefault();

        fetch(this.rewindUrlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && this.history.length > 0) {
                const last = this.history.pop();
                const card = last.card;
                this.currentIndex = Math.max(0, this.currentIndex - 1);

                card.style.display = '';
                card.classList.remove('animating-out', 'swipe-left', 'swipe-right', 'swipe-up');
                card.classList.add('animating-in');

                setTimeout(() => {
                    card.classList.remove('animating-in');
                }, 300);

                this.updateDeckState();
            }
        })
        .catch(err => console.error('Erreur rewind:', err));
    }

    // ── Mise à jour de l'affichage du deck ────────────────────────────────────

    get currentCard() {
        const availableCards = this.cardTargets.filter(c => c.style.display !== 'none');
        return availableCards[0] || null;
    }

    updateDeckState() {
        const availableCards = this.cardTargets.filter(c => c.style.display !== 'none');
        const totalCards = this.cardTargets.length;
        const remaining = availableCards.length;

        // Mise à jour de l'indicateur d'état (ex: OFFRE N° 1 / 3)
        if (this.hasStatusCounterTarget) {
            const currentNum = totalCards - remaining + 1;
            if (remaining > 0) {
                this.statusCounterTarget.textContent = `OFFRE RECOMMANDÉE N° ${currentNum} / ${totalCards}`;
            } else {
                this.statusCounterTarget.textContent = `AUCUNE OFFRE SUPPLÉMENTAIRE`;
            }
        }

        // Barre de progression
        if (this.hasProgressFillTarget) {
            const pct = totalCards > 0 ? ((totalCards - remaining) / totalCards) * 100 : 100;
            this.progressFillTarget.style.width = `${Math.min(100, Math.max(15, pct))}%`;
        }

        // Gestion des cartes fantômes derrière
        if (this.hasGhostCard1Target) {
            this.ghostCard1Target.style.display = remaining > 1 ? 'block' : 'none';
        }
        if (this.hasGhostCard2Target) {
            this.ghostCard2Target.style.display = remaining > 2 ? 'block' : 'none';
        }

        // État vide
        if (this.hasEmptyStateTarget && this.hasDeckWrapperTarget) {
            if (remaining === 0) {
                this.emptyStateTarget.style.display = 'flex';
                this.deckWrapperTarget.style.display = 'none';
            } else {
                this.emptyStateTarget.style.display = 'none';
                this.deckWrapperTarget.style.display = 'block';
            }
        }

        // État du bouton undo
        if (this.hasUndoBtnTarget) {
            this.undoBtnTarget.disabled = this.history.length === 0;
            this.undoBtnTarget.style.opacity = this.history.length === 0 ? '0.5' : '1';
        }
    }

    // ── Modal de Match ────────────────────────────────────────────────────────

    showMatchModal(data) {
        if (!this.hasMatchModalTarget) return;

        if (this.hasMatchCompanyNameTarget) {
            this.matchCompanyNameTarget.textContent = data.companyName || 'L\'entreprise';
        }
        if (this.hasMatchOfferTitleTarget) {
            this.matchOfferTitleTarget.textContent = data.offerTitle || '';
        }
        if (this.hasMatchCompanyLogoTarget && data.companyLogo) {
            this.matchCompanyLogoTarget.src = data.companyLogo;
        }

        this.matchModalTarget.style.display = 'flex';
        this.matchModalTarget.classList.add('active');
    }

    closeMatchModal() {
        if (!this.hasMatchModalTarget) return;
        this.matchModalTarget.classList.remove('active');
        setTimeout(() => {
            this.matchModalTarget.style.display = 'none';
        }, 200);
    }

    // ── Drag & Drop tactile pour les cartes ───────────────────────────────────

    initDragEvents() {
        this.cardTargets.forEach(card => {
            let startX = 0;
            let currentX = 0;
            let isDragging = false;

            const onStart = (clientX) => {
                if (card !== this.currentCard) return;
                isDragging = true;
                startX = clientX;
                card.style.transition = 'none';
            };

            const onMove = (clientX) => {
                if (!isDragging || card !== this.currentCard) return;
                currentX = clientX - startX;
                const rotate = currentX * 0.05;
                card.style.transform = `translateX(${currentX}px) rotate(${rotate}deg)`;
            };

            const onEnd = () => {
                if (!isDragging) return;
                isDragging = false;
                card.style.transition = 'transform 0.3s ease, opacity 0.3s ease';

                if (currentX > 120) {
                    this.like();
                } else if (currentX < -120) {
                    this.pass();
                } else {
                    card.style.transform = '';
                }
                currentX = 0;
            };

            // Événements Souris
            card.addEventListener('mousedown', (e) => onStart(e.clientX));
            window.addEventListener('mousemove', (e) => onMove(e.clientX));
            window.addEventListener('mouseup', () => onEnd());

            // Événements Touch
            card.addEventListener('touchstart', (e) => onStart(e.touches[0].clientX), { passive: true });
            window.addEventListener('touchmove', (e) => {
                if (isDragging && e.touches.length > 0) {
                    onMove(e.touches[0].clientX);
                }
            }, { passive: true });
            window.addEventListener('touchend', () => onEnd());
        });
    }
}
