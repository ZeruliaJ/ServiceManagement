(function () {
    function getLoader()
    {
        return document.getElementById('loader');
    }

    window.showGlobalLoader = function () {
        const loader = getLoader();
        if (!loader) return;
        loader.style.visibility = 'visible';
        loader.style.opacity = '1';
        document.documentElement.setAttribute('loader', 'enable');
    };

    window.hideGlobalLoader = function () {
        const loader = getLoader();
        if (!loader) return;
        loader.style.opacity = '0';
        loader.style.visibility = 'hidden';
        document.documentElement.setAttribute('loader', 'disable');
    };

    // Page load behavior (DON'T remove loader element)
    window.addEventListener('load', function () {
        window.hideGlobalLoader();
    });
})();
