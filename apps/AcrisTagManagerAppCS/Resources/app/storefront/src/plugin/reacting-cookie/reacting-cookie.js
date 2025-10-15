import { COOKIE_CONFIGURATION_UPDATE } from 'src/plugin/cookie/cookie-configuration.plugin';
import CookieStorage from 'src/helper/storage/cookie-storage.helper';
import Iterator from 'src/helper/iterator.helper';

document.$emitter.subscribe(COOKIE_CONFIGURATION_UPDATE, eventCallback);

function eventCallback(updatedCookies) {
    if (typeof updatedCookies.detail['acris-tag-manager'] !== 'undefined') {
        let cookieState = updatedCookies.detail['acris-tag-manager'];
        if(cookieState === true) {
            loadContent();
        }else{
            CookieStorage.unset('acris-tag-manager');
        }
    } else {
        // Do nothing
    }
}

function loadContent() {
    let elements = document.querySelectorAll('[data-acristagmanagercookie="true"]');

    CookieStorage.setItem('acris-tag-manager', '1', '30');

    // Load content if cookie is set
    Iterator.iterate(elements, (el) => {
        switch (el.tagName.toLowerCase()) {
            case 'script':
                handleLoadContentByScript(el);
                break;
        }
    });
}

function handleLoadContentByScript(el) {
    let cookieId = 'acris-tag-manager';

    let script = document.createElement('script'),
        scriptHtml = el.innerHTML,
        scriptSrc = el.src,
        scriptAsync = el.async,
        scriptDefer = el.defer;

    script.type = "text/javascript";
    script.classList.add('acris-clone');

    if(scriptHtml)
        script.innerHTML = scriptHtml;

    if(scriptSrc)
        script.src = scriptSrc;

    if(scriptAsync)
        script.async = scriptAsync;

    if(scriptDefer)
        script.defer = scriptDefer;

    el.after(script);
}