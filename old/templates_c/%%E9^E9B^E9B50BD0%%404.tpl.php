<?php /* Smarty version 2.6.20, created on 2016-01-23 13:34:15
         compiled from 404.tpl */ ?>
<h4>Что-то пошло не так</h4>
<p>Кажется эта страница потерялась... или ее вообще никогда не было?</p>
<p>Попробуй еще так:</p>
<p id="redirect"></p>

<script>
    $('h1').text('Глюк');
    $('p#redirect').html('<a href="http:/'+window.location.pathname+'">'+window.location.pathname+'</a>');
</script>