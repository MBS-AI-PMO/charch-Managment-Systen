import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

/**
 * Pop a media-library window. The picker page (admin/media?picker=1)
 * sends back the chosen URL via window.postMessage and closes itself.
 */
function openMediaPicker(onSelect) {
    const w = window.open(
        '/admin/media?picker=1',
        'media',
        'width=1100,height=720,resizable=yes,scrollbars=yes',
    );
    const listener = (e) => {
        if (!e.data || e.data.kind !== 'media-picked') return;
        try { onSelect(e.data.url, e.data.alt || ''); } catch (err) { console.error(err); }
        window.removeEventListener('message', listener);
        if (w && !w.closed) w.close();
    };
    window.addEventListener('message', listener);
}
window.openMediaPicker = openMediaPicker;

/**
 * Upload adapter that posts CKEditor's drag/dropped or pasted files
 * straight to the admin media endpoint and returns the persisted URL.
 */
class MediaApiAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(
            (file) =>
                new Promise((resolve, reject) => {
                    const fd = new FormData();
                    fd.append('files[]', file);
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                    fetch('/admin/media', {
                        method: 'POST',
                        body: fd,
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf || '',
                        },
                        credentials: 'same-origin',
                    })
                        .then((r) =>
                            r.ok
                                ? r.json()
                                : r.text().then((t) => Promise.reject(new Error(t || `HTTP ${r.status}`))),
                        )
                        .then((j) => {
                            const first = j.items && j.items[0];
                            if (!first) return reject(new Error('Upload returned no items'));
                            resolve({ default: first.url });
                        })
                        .catch(reject);
                }),
        );
    }

    abort() {
        // Nothing to clean up; we don't expose an abort handle.
    }
}

function MediaApiAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) =>
        new MediaApiAdapter(loader);
}

/**
 * Boot CKEditor on every `.ck-editor` textarea on the page. Idempotent —
 * already-initialised elements are skipped via a data flag.
 */
window.initCKEditor = (selector = '.ck-editor') => {
    document.querySelectorAll(selector).forEach((el) => {
        if (el.dataset.ckReady === '1') return;
        el.dataset.ckReady = '1';
        ClassicEditor.create(el, {
            extraPlugins: [MediaApiAdapterPlugin],
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'underline',
                'link',
                'blockQuote',
                '|',
                'bulletedList',
                'numberedList',
                '|',
                'fontFamily',
                'fontSize',
                'fontColor',
                'fontBackgroundColor',
                'alignment',
                '|',
                'imageUpload',
                'insertTable',
                'mediaEmbed',
                '|',
                'undo',
                'redo',
                'sourceEditing',
            ],
            fontFamily: {
                options: [
                    'default',
                    'Inter, sans-serif',
                    '"Fraunces", serif',
                    'Lora, serif',
                    'Georgia, serif',
                    'system-ui',
                ],
            },
            mediaEmbed: { previewsInData: true },
        }).catch(console.error);
    });
};

document.addEventListener('DOMContentLoaded', () => window.initCKEditor());
