<?php wp_footer(); ?>
<!--Footer ACF-->
<?php


$code_before_end_of_body_tag = get_field('code_before_end_of_body_tag', 'options');
$footer_logo = get_field('footer_logo', 'options');
?>
<!--region footer-->
<footer>

</footer>
</main>
<?= $code_before_end_of_body_tag ?>
</body>
</html>
