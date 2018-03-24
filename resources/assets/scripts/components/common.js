import hljs from 'highlight.js';

export default {
    ready: () => {

        $('pre code').each(function(i, block) {
            hljs.highlightBlock(block);
        });

        $('.select2').select2();

        $('.datatable').DataTable();
    },
};
