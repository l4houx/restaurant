import './bootstrap.js';

import '@fortawesome/fontawesome-free/css/all.css';
import './styles/app.css';

import { Tooltip, Toast, Modal } from 'bootstrap';

document.querySelector('body').addEventListener('click', (e) => {
    if (
        !document.querySelector('.navbar-collapse').contains(e.target) &&
        document.querySelector('.navbar-collapse').classList.contains('show')
    ) {
        e.preventDefault();
        document.querySelector('.navbar-collapse')
            .classList
            .remove('show');
    }
});

if (document.querySelector('body').classList.contains('purchase_form')) {
    const renderPaymentMethod = () => {
        if (document.getElementById('purchase_form_mode_0').checked) {
            document.getElementById('bank-wire')
                .classList
                .remove('d-none');
            document.getElementById('check')
                .classList
                .add('d-none');
        } else if (document.getElementById('purchase_form_mode_1').checked) {
            document.getElementById('bank-wire')
                .classList
                .add('d-none');
            document.getElementById('check')
                .classList
                .remove('d-none');
        } else {
            document.getElementById('bank-wire')
                .classList
                .add('d-none');
            document.getElementById('check')
                .classList
                .add('d-none');
        }
    };

    renderPaymentMethod();
    Array.from(document.querySelectorAll('input[name="purchase_form[mode]"]'))
        .forEach((e) => e.addEventListener('change', renderPaymentMethod));
}

Array.from(document.querySelectorAll('.modal-onload'))
    .map((e) => new Modal(e, { backdrop: false }))
    .forEach((modal) => modal.show());

Array.from(document.querySelectorAll('[data-bs-toggle=tooltip]'))
    .map((e) => new Tooltip(e));

Array.from(document.querySelectorAll('.input-group-password'))
    .forEach((e) => {
        const button = e.querySelector('button');
        const input = e.querySelector('input');
        const state = {
            password: 'text',
            text: 'password',
        };
        button.addEventListener('click', () => {
            input.setAttribute('type', state[input.getAttribute('type')]);
        });
    });

Array.from(document.querySelectorAll('.slider'))
    .map((slider) => {
        const min = parseInt(slider.dataset.min);
        const max = parseInt(slider.dataset.max);
        const minTarget = document.querySelector(slider.dataset.minTarget);
        const maxTarget = document.querySelector(slider.dataset.maxTarget);
        noUiSlider.create(slider, {
            start: [parseInt(minTarget.value), parseInt(maxTarget.value)],
            tooltips: true,
            connect: true,
            step: 5,
            range: {
                'min': min,
                'max': max,
            },
            format: {
                to: (value) => parseInt(value),
                from: (value) => parseInt(value),
            },
        }).on('update', function (values, handle) {
            const value = values[handle];
            if (handle) {
                maxTarget.value = value;
            } else {
                minTarget.value = value;
            }
        });
    });

const sidebarTogglers = Array.from(
    document.querySelectorAll('.sidebar-toggler'),
);

sidebarTogglers.map((toggler) => toggler.addEventListener('click', () => {
    document.querySelector('body').classList.toggle('sidebar-open');
    sidebarTogglers.map((e) => e
        .setAttribute('aria-expanded', document.querySelector('body')
            .classList
            .contains('sidebar-open')));
}));

$(document).ready(function () {
    function Toastr() {
        Array.from(document.querySelectorAll('.toast'))
        .map((toast) => (new Toast(toast)).show());
    }

    // Product favorites ajax add and remove
    $(document).on("click", ".product-favorites-new, .product-favorites-remove", function () {
        const thisButton = $(this);
        if (thisButton.attr("data-action-done") == "1") {
            thisButton.unbind("click");
            return false;
        }
        function ajaxRequest(thisButton) {
            $.ajax({
                type: "GET",
                url: thisButton.data('target'),
                beforeSend: function () {
                    thisButton.attr("data-action-done", "1");
                    thisButton.html("<i class='fas fa-spinner fa-spin'></i>");
                },
                success: function (response) {
                    if (response.hasOwnProperty('success')) {
                        if (thisButton.hasClass('product-favorites-new')) {
                            thisButton.html('<i class="fas fa-heart"></i>');
                        } else {
                            thisButton.html('<i class="far fa-heart"></i>');
                        }
                        thisButton.attr("title", response.success).tooltip("_fixTitle");
                        Toastr('success', '', response.success);
                    } else if (response.hasOwnProperty('error')) {
                        thisButton.html('<i class="far fa-heart"></i>');
                        thisButton.attr("title", response.error).tooltip("_fixTitle");
                        Toastr('error', '', response.error);
                    } else {
                        thisButton.html('<i class="far fa-heart"></i>');
                        thisButton.attr("title", 'An error has occured').tooltip("_fixTitle");
                        Toastr('error', '', 'An error has occured');
                    }
                },
                error: function (xhr, status, error) {

                }
            });
        }

        ajaxRequest(thisButton);
    });
});
