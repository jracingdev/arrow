/**
 * Helpers anti-crash Firebase (path vazio, índice ausente, defaults BR).
 * Incluir após firebase-init em admin/store/website.
 */
(function (global) {
    'use strict';

    function isValidDocId(id) {
        return id != null && String(id).trim() !== '' && String(id) !== 'undefined' && String(id) !== 'null';
    }

    function warnOnce(key, message) {
        global.__arrowFsWarn = global.__arrowFsWarn || {};
        if (global.__arrowFsWarn[key]) return;
        global.__arrowFsWarn[key] = true;
        console.warn(message);
    }

    /**
     * sections where isActive + orderBy('order') com fallback se índice faltar.
     */
    async function fetchActiveSectionsOrdered(db) {
        db = db || global.database;
        if (!db) throw new Error('ArrowFirestore: database não disponível');
        var base = db.collection('sections').where('isActive', '==', true);
        try {
            return await base.orderBy('order').get();
        } catch (indexErr) {
            warnOnce(
                'sections_order',
                'sections orderBy falhou (índice ausente?). Usando fallback. Deploy: firebase deploy --only firestore:indexes — ' +
                    (indexErr && indexErr.message)
            );
            return await base.get();
        }
    }

    /** .doc(id).get() só se id válido; senão null (evita FirebaseError empty path). */
    async function safeDocGet(collectionName, id, db) {
        db = db || global.database;
        if (!isValidDocId(id) || !db) return null;
        try {
            return await db.collection(collectionName).doc(String(id).trim()).get();
        } catch (err) {
            console.error('ArrowFirestore.safeDocGet', collectionName, id, err);
            return null;
        }
    }

    /**
     * Define DDI no #country_selector (select2). Padrão Brasil 55.
     * @param {string} [phoneCode] ex: '+55' ou '55'
     * @param {string} [selector] default #country_selector
     */
    function setDefaultCountryCode(phoneCode, selector) {
        selector = selector || '#country_selector';
        var $el = global.jQuery ? global.jQuery(selector) : null;
        if (!$el || !$el.length) return;
        var code = (phoneCode || '55').toString().replace('+', '').trim() || '55';
        var $option = $el.find('option').filter(function () {
            return global.jQuery(this).val() === code;
        });
        if ($option.length > 0) {
            $el.val(code).trigger('change');
        } else if ($el.find("option[value='55']").length) {
            $el.val('55').trigger('change');
        }
    }

    /** Carrega defaultCountryCode de globalSettings ou força +55. */
    function applyGlobalOrBrCountry(selector) {
        selector = selector || '#country_selector';
        setDefaultCountryCode('55', selector);
        var db = global.database;
        if (!db) return Promise.resolve();
        return db
            .collection('settings')
            .doc('globalSettings')
            .get()
            .then(function (snapshot) {
                var gs = snapshot.exists ? snapshot.data() : null;
                if (gs && gs.defaultCountryCode) {
                    setDefaultCountryCode(gs.defaultCountryCode, selector);
                } else if (gs) {
                    db.collection('settings')
                        .doc('globalSettings')
                        .set({ defaultCountryCode: '+55' }, { merge: true })
                        .catch(function () {});
                }
            })
            .catch(function (err) {
                console.error('ArrowFirestore.applyGlobalOrBrCountry', err);
                setDefaultCountryCode('55', selector);
            });
    }

    /** Null-safe access a snapshots.data(). */
    function safeData(snap) {
        if (!snap || typeof snap.data !== 'function') return null;
        try {
            return snap.data() || null;
        } catch (e) {
            return null;
        }
    }

    global.ArrowFirestore = {
        isValidDocId: isValidDocId,
        fetchActiveSectionsOrdered: fetchActiveSectionsOrdered,
        safeDocGet: safeDocGet,
        setDefaultCountryCode: setDefaultCountryCode,
        applyGlobalOrBrCountry: applyGlobalOrBrCountry,
        safeData: safeData
    };
})(typeof window !== 'undefined' ? window : this);
