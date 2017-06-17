<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/github.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<div class="wrap">
    <?php
    if (!class_exists('Parsedown')) {
        require_once __DIR__ . '/../lib/Parsedown.php';
    }

    echo str_replace('language-text', 'language-wp', Parsedown::instance()->parse(file_get_contents(__DIR__ . '/../README.MD')));
    ?>
</div>
<script>
    hljs.registerLanguage('wp', function(){
        return {
            subLanguage: 'xml'
        };
    });
    hljs.configure({
        tabReplace: '    ',
        classPrefix: ''
    });
    hljs.initHighlighting();
</script>
