import '../css/app.scss';
import $ from 'jquery';
import 'bootstrap';

import 'bootstrap-notify';

import 'bootstrap-datepicker';

import 'select2';

import test from './student_homepage'

window.$ = $;

$('.input-group-btn').on('click', function () {
    alert("test");
});

$( document ).ready(function() {
    console.log( "ready!" );
});