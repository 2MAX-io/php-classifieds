"use strict";

new Cleave('.input-money', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand',
    numeralPositiveOnly: true,
    delimiter: '{{ getCleaveConfig().delimiter }}',
    numeralDecimalMark: '{{ getCleaveConfig().numeralDecimalMark }}'
});