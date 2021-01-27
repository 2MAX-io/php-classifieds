"use strict";

new Cleave('.input-money', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand',
    numeralPositiveOnly: true,
    delimiter: app.getDataForJs()['cleaveConfig']['delimiter'],
    numeralDecimalMark: app.getDataForJs()['cleaveConfig']['numeralDecimalMark']
});
