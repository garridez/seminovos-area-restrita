
module.exports = (function (window, document) {
    var googletag = window.googletag || {};
    googletag.cmd = googletag.cmd || [];

    var $each = function (seletorOrArray, cb) {
        var data = seletorOrArray;
        if (typeof seletorOrArray === 'string') {
            data = document.querySelectorAll(seletorOrArray);
        }
        if (cb) {
            [].forEach.call(data, cb);
        }
        return data;
    };

    var slots = {};
    var countSlotsUsed = {};
    var adUnitPathAttr = 'data-ad-path';
    var slotsDefinition = getSlotsDefinition();
    var addEvent = function (event, cb, target) {
        target = target || document;
        target.addEventListener(event, cb, false);
    };
    var pubads;
    init();

    function init() {
        pushCmds();
    }
    function pushCmds() {
        googletag.cmd.push(function cmdDefinePubads() {
        });
        googletag.cmd.push(function cmdDefinePubads() {
            pubads = googletag.pubads();
        });
        googletag.cmd.push(function cmdDefineSlot() {
            $each('[' + adUnitPathAttr + ']', createSlot);
        });
        googletag.cmd.push(function enableServices() {
            pubads.enableSingleRequest();
            googletag.enableServices();
        });
        googletag.cmd.push(function displaySlots() {
            var slotsToDisplay = [];
            $each('[' + adUnitPathAttr + ']', function (e) {
                var adPath = e.getAttribute(adUnitPathAttr);
                if (e.id) {
                    // O elemento já está associado a um slot
                    slotsToDisplay.push(e.id);
                    return;
                }

                if (slots[adPath]) {
                    for (var id in slots[adPath]) {
                        var slot = slots[adPath][id];
                        if (slot.element === null) {
                            slot.element = e;
                            e.id = id;
                        }
                    }
                }
                if (!e.id) {
                    e = createSlot(e);
                }
                if (e && e.id) {
                    slotsToDisplay.push(e.id);
                }
            });

            $each(slotsToDisplay, function (id) {
                googletag.display(id);
            });
        });
        googletag.cmd.push(function cmdResize() {
            var resizeTimeout;
            window.addEventListener('resize', function () {
                if (resizeTimeout) {
                    clearTimeout(resizeTimeout);
                }
                resizeTimeout = setTimeout(function () {
                    resizeTimeout = null;
                    pubads.refresh();
                }, 1000);
            }, false);
        });
    }

    function createSlot(e) {
        var adPath = e.getAttribute(adUnitPathAttr);
        var slotConfig = slotsDefinition;
        $each(adPath.split('.'), function (a) {
            if (slotConfig[a]) {
                slotConfig = slotConfig[a];
            }
        });
        if (typeof slotConfig === 'function') {
            slotConfig = slotConfig();
        }
        if (!slotConfig || !slotConfig.adUnitPath) {
            console.log('Config não encontrado', adPath);
            return false;
        }
        countSlotsUsed[adPath] = countSlotsUsed[adPath] || 0;
        countSlotsUsed[adPath]++;

        e.id = adPath + '.' + (countSlotsUsed[adPath] - 1);

        var slot = googletag.defineSlot(slotConfig.adUnitPath, slotConfig.size, e.id);

        if (slotConfig.sizeMapping) {
            slot.defineSizeMapping(slotConfig.sizeMapping);
        }
        slot.addService(pubads);
        slot.setCollapseEmptyDiv(true);
        slots[adPath] = slots[adPath] || {};
        slots[adPath][e.id] = {
            slot: slot,
            element: e instanceof Element || e instanceof HTMLDocument ? e : null
        };
        return e;
    }

    function getSlotsDefinition() {
        var tamanhosPossives = [
            [88, 31],
            [120, 240], [120, 600], [120, 60], [120, 90], [125, 125], [160, 600], [180, 150],
            [200, 200], [234, 60], [240, 400], [250, 250],
            [300, 100], [300, 250], [300, 600], [320, 50], [336, 280], [320, 100],
            [468, 60],
            [728, 90],
            [970, 250], [970, 90]
        ];

        var getSizesByRange = function (minW, maxW, minH, maxH, orderByW) {
            maxW = maxW || 0;
            minW = minW || 0;
            minH = minH || 0;
            maxH = maxH || 0;
            orderByW = orderByW === undefined ? true : orderByW;

            var sizes = [];
            $each(tamanhosPossives, function (size) {
                var w = size[0];
                var h = size[1];
                if (
                        w <= maxW && w >= minW &&
                        h <= maxH && h >= minH
                        ) {
                    sizes.push(size);
                }
            });

            var hFirst = function (a, b) {
                return a[1] <= b[1] ? 1 : -1;
            };
            var wFirst = function (a, b) {
                return a[0] <= b[0] ? 1 : -1;
            };
            return sizes
                    .sort(orderByW ? hFirst : wFirst)
                    .sort(orderByW ? wFirst : hFirst);
        };

        return {
            meus_anucios: {
                topo: function () {
                    return {
                        adUnitPath: '/131912127/MA/MEUS_VEICULOS',
                        size: getSizesByRange(0, 1000, 0, 100),
                        sizeMapping: [
                            [[975, 0], getSizesByRange(0, 970, 0, 100)],
                            [[754, 0], getSizesByRange(0, 750, 0, 100)],
                            [[494, 0], getSizesByRange(0, 470, 0, 100)],
                            [[328, 0], getSizesByRange(0, 300, 0, 100)],
                            [[0, 0], getSizesByRange(0, 300, 0, 200)]
                        ]
                    };
                }
            },
        };
    }
    /**
     * Descomente para debuggar o with do site
     * Útil para ajudar a definir os tamanhos anúncios de acordo com o grid do boostrap
     * O boostrap usa o "innerWidth" para os breaking points, já o mapping do ADS usa o clientWidth
     * Então ative o debug para ver qual é a correspondência de cada um
     */
    // enableDebugWidth();
    function enableDebugWidth() {
        addEvent('DOMContentLoaded', function () {
            var div = document.createElement('div');
            var style = div.style;
            style.position = 'fixed';
            style.top = 0;
            style.left = 0;
            style.zIndex = 1000;
            style.backgroundColor = 'white';
            style.padding = '2px 5px';
            style.borderRadius = '20px';

            var ww = document.createElement('div');
            ww.title = 'ADS Mapping real width';
            var bs = document.createElement('div');
            ww.title = 'window.innerWidth';
            div.appendChild(ww);
            div.appendChild(bs);
            document.body.appendChild(div);

            var browserWidth = function () {
                return window.innerWidth && document.documentElement.clientWidth ? Math.min(window.innerWidth, document.documentElement.clientWidth) : window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            };
            setInterval(function () {
                var text = browserWidth();
                if (ww.innerText !== text + '') {

                    ww.innerText = text;
                }
                if (bs.innerText !== innerWidth + '') {
                    bs.innerText = innerWidth;
                }
            }, 500);
        });
    }


}(window, document));