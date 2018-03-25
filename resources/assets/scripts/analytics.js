import uuidv4 from 'uuid/v4';

const dimensions = {
    TRACKING_VERSION: 'dimension1',
    CLIENT_ID: 'dimension2',
    WINDOW_ID: 'dimension3',
    HIT_ID: 'dimension4',
    HIT_TIME: 'dimension5',
    HIT_TYPE: 'dimension6',
    WP_ENV: 'dimension7',
    COMMITHASH: 'dimension8',

};

const metrics = {
    RESPONSE_END_TIME: 'metric1',
    DOM_LOAD_TIME: 'metric2',
    WINDOW_LOAD_TIME: 'metric3',
    MAX_SCROLL_PERCENTAGE: 4,
    PAGE_VISIBLE_TIME: 5,
    PAGE_LOADS: 6,
};

const TRACKING_VERSION = '1';

const ga = window.ga = window.ga || ((...args) => (ga.q = ga.q || []).push(args));

const Analytics = {

    loadPlugins: function() {
        ga('require', 'cleanUrlTracker', {
            trailingSlash: 'add',
        });
        ga('require', 'eventTracker', {
            events: ['click', 'auxclick', 'contextmenu', 'change', 'submit' ],
            hitFilter: function(model, element) {
                // add input
                if ($(element).is('input')) {
                    model.set('eventLabel', $(element).val(), true);
                }
            },
        });
        ga('require', 'outboundLinkTracker', {
            events: ['click', 'auxclick', 'contextmenu'],
        });
        ga('require', 'maxScrollTracker', {
            maxScrollMetricIndex: metrics.MAX_SCROLL_PERCENTAGE,
        });
        ga('require', 'pageVisibilityTracker', {
            sendInitialPageview: true,
            pageLoadsMetricIndex: metrics.PAGE_LOADS,
            visibleMetricIndex: metrics.PAGE_VISIBLE_TIME,
        });
    },

    customDimensions: function() {
        ga('set', dimensions.TRACKING_VERSION, TRACKING_VERSION);
        ga('set', dimensions.WP_ENV, process.env.WP_ENV);
        // eslint-disable-next-line no-undef
        ga('set', dimensions.COMMITHASH, COMMITHASH);
        ga('set', dimensions.WINDOW_ID, uuidv4());

        ga(function(tracker) {
            const clientId = tracker.get('clientId');
            tracker.set(dimensions.CLIENT_ID, clientId);

            const originalBuildHitTask = tracker.get('buildHitTask');
            tracker.set('buildHitTask', function(model) {
                model.set(dimensions.HIT_ID, uuidv4(), true);
                model.set(dimensions.HIT_TIME, String(+new Date), true);
                model.set(dimensions.HIT_TYPE, model.get('hitType'), true);

                originalBuildHitTask(model);
            });
        });
    },

    init: function() {
        ga('create', process.env.GA_ID, 'auto');
        ga('set', 'transport', 'beacon');

        Analytics.loadPlugins();
        Analytics.customDimensions();
        Analytics.sendNavigationTimingMetrics();
        Analytics.customEvents();
    },

    sendNavigationTimingMetrics: function() {
        // Only track performance in supporting browsers.
        if (!(window.performance && window.performance.timing)) return;

        // If the window hasn't loaded, run this function after the `load` event.
        if (document.readyState !== 'complete') {
            window.addEventListener('load', Analytics.sendNavigationTimingMetrics);
            return;
        }

        const nt = performance.timing;
        const navStart = nt.navigationStart;

        const responseEnd = Math.round(nt.responseEnd - navStart);
        const domLoaded = Math.round(nt.domContentLoadedEventStart - navStart);
        const windowLoaded = Math.round(nt.loadEventStart - navStart);

        // In some edge cases browsers return very obviously incorrect NT values,
        // e.g. 0, negative, or future times. This validates values before sending.
        const allValuesAreValid = (...values) => {
            return values.every((value) => value > 0 && value < 1e6);
        };

        if (allValuesAreValid(responseEnd, domLoaded, windowLoaded)) {
            window.ga('send', 'event', {
                eventCategory: 'Navigation Timing',
                eventAction: 'track',
                nonInteraction: true,
                [metrics.RESPONSE_END_TIME]: responseEnd,
                [metrics.DOM_LOAD_TIME]: domLoaded,
                [metrics.WINDOW_LOAD_TIME]: windowLoaded,
            });
        }
    },

    customEvents: function() {
        $('.widget-contact-form-7 form').on('submit', function() {
            window.ga('send', 'event', {
                eventCategory: 'Newsletter Register',
                eventAction: 'Submit',
                eventLabel: '',
            });
        });
    },

};

Analytics.init();
