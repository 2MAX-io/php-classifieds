"use strict";

$('.input-datetime').toArray().forEach(function (field) {
    new Cleave(field, {
        delimiters: ['-', '-', ' ', ':', ':'],
        blocks: [4, 2, 2, 2, 2, 2]
    });
});