import hljs from 'highlight.js';

export default {
    ready: () => {

        $('pre code').each(function(i, block) {
            hljs.highlightBlock(block);
        });

        $('.select2').each(function() {
            $(this).select2($(this).data());
        });

        $('.datatable').DataTable();
    },
};
