document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('tidio-system-info');
    var selectRange = function() {
        textarea.focus();
        textarea.setSelectionRange(0, textarea.value.length);
    };

    var copyButton = document.getElementById('tidio-copy-system-info');
    copyButton.onclick = function() {
        selectRange();
        document.execCommand('copy');
    };

    var downloadButton = document.getElementById('tidio-download-system-info');
    downloadButton.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(textarea.innerHTML));
    downloadButton.setAttribute('download', 'tidio-plugin-system-info.txt');
});
