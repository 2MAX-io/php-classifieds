"use strict";

new Cleave('.input-money', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand',
    numeralPositiveOnly: true,
    delimiter: app.getJsonDataCached()['cleaveConfig']['delimiter'],
    numeralDecimalMark: app.getJsonDataCached()['cleaveConfig']['numeralDecimalMark']
});
