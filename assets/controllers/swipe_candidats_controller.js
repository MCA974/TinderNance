import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card', 'emptyState', 'toast', 'toastText', 'pendingCount', 'swipedCount'];
    static values = {
        offerId: Number,
        swipeUrl: String
    };

    connect() {
        this.currentIndex = 0;
        this.todaySwiped = 0;
        this._showCurrentCard();

        // Listen for keyboard shortcuts
        this.boundKeyHandler = this.handleKeydown.bind(this);
        window.addEventListener('keydown', this.boundKeyHandler);
    }

    disconnect() {
        window.removeEventListener('keydown', this.boundKeyHandler);
    }

    handleKeydown(event) {
        // Ignore if user is typing in an input
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(event.target.tagName)) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            this.pass();
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            this.match();
        } else if (event.key === 'b' || event.key === 'B') {
            event.preventDefault();
            this.wait();
        }
    }

    pass(event) {
        if (event) event.preventDefault();
        const currentCard = this._getCurrentCard();
        if (!currentCard) return;

        currentCard.classList.add('sc-swipe-left');
        const candidateId = currentCard.dataset.candidateId;
        this._sendSwipe(candidateId, 'DISLIKE');

        setTimeout(() => {
            this._advanceCard();
        }, 350);
    }

    match(event) {
        if (event) event.preventDefault();
        const currentCard = this._getCurrentCard();
        if (!currentCard) return;

        currentCard.classList.add('sc-swipe-right');
        const candidateId = currentCard.dataset.candidateId;
        this._sendSwipe(candidateId, 'LIKE');

        setTimeout(() => {
            this._advanceCard();
        }, 350);
    }

    wait(event) {
        if (event) event.preventDefault();
        const currentCard = this._getCurrentCard();
        if (!currentCard) return;

        currentCard.classList.add('sc-swipe-left');
        const candidateId = currentCard.dataset.candidateId;
        this._sendSwipe(candidateId, 'WAIT');

        setTimeout(() => {
            this._advanceCard();
        }, 350);
    }

    _getCurrentCard() {
        if (this.currentIndex < this.cardTargets.length) {
            return this.cardTargets[this.currentIndex];
        }
        return null;
    }

    _showCurrentCard() {
        this.cardTargets.forEach((card, index) => {
            card.style.display = index === this.currentIndex ? 'block' : 'none';
        });

        if (this.currentIndex >= this.cardTargets.length) {
            if (this.hasEmptyStateTarget) {
                this.emptyStateTarget.style.display = 'flex';
            }
        } else {
            if (this.hasEmptyStateTarget) {
                this.emptyStateTarget.style.display = 'none';
            }
        }
    }

    _advanceCard() {
        this.currentIndex++;
        this.todaySwiped++;

        // Update counts in UI
        if (this.hasPendingCountTarget) {
            const remaining = Math.max(0, this.cardTargets.length - this.currentIndex);
            this.pendingCountTarget.textContent = remaining;
        }

        if (this.hasSwipedCountTarget) {
            const current = parseInt(this.swipedCountTarget.textContent, 10) || 0;
            this.swipedCountTarget.textContent = current + 1;
        }

        this._showCurrentCard();
    }

    async _sendSwipe(candidateId, action) {
        if (!this.swipeUrlValue) return;

        try {
            const response = await fetch(this.swipeUrlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    candidateId: candidateId,
                    offerId: this.offerIdValue,
                    action: action
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.isMatch) {
                    this._showMatchToast(data.candidateName || 'ce candidat');
                }
            }
        } catch (e) {
            // Silently ignore network / DB errors in dev
        }
    }

    _showMatchToast(name) {
        if (!this.hasToastTarget) return;

        if (this.hasToastTextTarget) {
            this.toastTextTarget.textContent = `🎉 C'est un Match instantané avec ${name} !`;
        }
        this.toastTarget.classList.add('show');

        setTimeout(() => {
            this.toastTarget.classList.remove('show');
        }, 4000);
    }
}
