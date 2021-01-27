app.paymentWait = {};

app.paymentWait.init = function () {
    app.paymentWait.refresh();
};

app.paymentWait.refresh = function () {
    var paymentAppToken = app.getDataForJs()['paymentAppToken'];
    $.ajax(Routing.generate('app_payment_wait_refresh_ajax', []), {
        type: 'POST',
        dataType: 'json',
        data: {
            paymentAppToken: paymentAppToken
        }
    }).done(function (data) {
        app.debugLog(data);

        $('#js__lastRefreshTime').text(data['lastRefreshTime']);

        if (data['paymentComplete'] === true) {
            let $jsPaymentStatus = $('#js__paymentStatus');
            $jsPaymentStatus.addClass('text-success');
            $jsPaymentStatus.removeClass('text-danger');
            $jsPaymentStatus.text(Translator.trans('trans.Success, redirecting...'));

            window.location.href = Routing.generate('app_payment_wait_redirect', {paymentAppToken: paymentAppToken});
        } else {
            setTimeout(function () {
                app.paymentWait.refresh();
            }, 800);
        }

    }).fail(function () {
        setTimeout(function () {
            app.paymentWait.refresh();
        }, 5000);
    });
};

app.paymentWait.init();
