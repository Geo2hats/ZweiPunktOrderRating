import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';

export default class OrderRatingPlugin extends Plugin {
    init() {
        this._httpClient = new HttpClient();
        this._reviewSubmitButton = DomAccess.querySelector(document, this.options.reviewSubmitButton, false);
        this._reviewInputs = DomAccess.querySelectorAll(document, this.options.reviewInputs, false);
        this._reviewFormContainer = DomAccess.querySelector(document, this.options.reviewFormContainer, false);
        this._orderNumberContainer = DomAccess.querySelector(document, this.options.orderNumberContainer, false);
        this._reviewContentInput = DomAccess.querySelector(document, this.options.reviewContentInput, false);
        this._csrfTockenInput = DomAccess.querySelector(document, this.options.csrfTockenInput, false);
        this._flashbagsContainer = DomAccess.querySelector(document, this.options.flashbagsContainer, false);
        
        this._registerEvents();
    }

    _registerEvents() {
        if (this._reviewSubmitButton) {
            this._reviewSubmitButton.addEventListener('click', (event) => {
                event.preventDefault();
                this._onFormSubmit();
            });
        }

        if (this._reviewInputs) {
            this._reviewInputs.forEach((input) => {
                input.addEventListener('change', (event) => {
                    event.preventDefault();
                    this._onChangeRatingStars();
                });
            });
        }
    }

    _onChangeRatingStars() {
        if (this._reviewFormContainer) {
            this._reviewFormContainer.style.display = 'block';
        }
    }

    _onFormSubmit() {
        let rating = 0;
        
        if (this._reviewInputs) {
            this._reviewInputs.forEach((input, index) => {
                if (input.checked) {
                    rating = this._reviewInputs.length - index;
                }
            });
        }

        const orderNumber = this._orderNumberContainer.getAttribute('data-order-number');
        const comment = this._reviewContentInput.value;

        if (rating) {
            let data = {
                reviewCount: rating,
                ordernumber: orderNumber,
                comment: comment
            };

            this._httpClient.post('/order/rating', JSON.stringify(data), this._setContent.bind(this));
        }
    }

    _setContent(response) {
        console.log(response);
        let data = JSON.parse(response);
        console.log(data);
        
        if (data.success) {
            this._displayNotification(data.message, 'success');
        } else {
            this._displayNotification(data.message, 'danger');
        }
    }

    _displayNotification(message, type) {
        if (type === null) {
            type = 'danger';
        }

        let icon = type === 'danger' 
            ? '<span class="icon icon-blocked">\n<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24"><defs><path d="M12 24C5.3726 24 0 18.6274 0 12S5.3726 0 12 0s12 5.3726 12 12-5.3726 12-12 12zm0-2c5.5228 0 10-4.4772 10-10S17.5228 2 12 2 2 6.4772 2 12s4.4772 10 10 10zm4.2929-15.7071c.3905-.3905 1.0237-.3905 1.4142 0 .3905.3905.3905 1.0237 0 1.4142l-10 10c-.3905.3905-1.0237.3905-1.4142 0-.3905-.3905-.3905-1.0237 0-1.4142l10-10z" id="icons-default-blocked"></path></defs><use xlink:href="#icons-default-blocked" fill="#758CA3" fill-rule="evenodd"></use></svg>\n</span>'
            : '<span class="icon icon-checkmark-circle">\n<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24"><defs><path d="M24 12c0 6.6274-5.3726 12-12 12S0 18.6274 0 12 5.3726 0 12 0s12 5.3726 12 12zM12 2C6.4772 2 2 6.4772 2 12s4.4772 10 10 10 10-4.4772 10-10S17.5228 2 12 2zM7.7071 12.2929 10 14.5858l6.2929-6.293c.3905-.3904 1.0237-.3904 1.4142 0 .3905.3906.3905 1.0238 0 1.4143l-7 7c-.3905.3905-1.0237.3905-1.4142 0l-3-3c-.3905-.3905-.3905-1.0237 0-1.4142.3905-.3905 1.0237-.3905 1.4142 0z" id="icons-default-checkmark-circle"></path></defs><use xlink:href="#icons-default-checkmark-circle" fill="#758CA3" fill-rule="evenodd"></use></svg>\n</span>';

        if (this._flashbagsContainer) {
            let notification = `<div role="alert" class="alert alert-${type} alert-has-icon d-flex">${icon}<div class="alert-content-container"><div class="alert-content">${message}</div></div></div>`;
            this._flashbagsContainer.innerHTML = notification;
            window.scrollTo(0, 0);
        }
    }

    static options = {
        reviewSubmitButton: '#reviewSubmitButton',
        reviewInputs: '.finish-raiting .finish-reviews-stars-container input',
        reviewFormContainer: '.finish-reviews-show-after',
        orderNumberContainer: '.finish-ordernumber',
        reviewContentInput: '#finishReviewContent',
        csrfTockenInput: '.finish-raiting input[name="_csrf_token"]',
        flashbagsContainer: '.flashbags'
    };
}
