// We use an "Immediate Function" to initialize the application to avoid leaving anything behind in the global scope
function Deferred() {
	// update 062115 for typeof
	if (typeof(Promise) != 'undefined' && Promise.defer) {
		//need import of Promise.jsm for example: Cu.import('resource:/gree/modules/Promise.jsm');
		return Promise.defer();
	} else if (typeof(PromiseUtils) != 'undefined'  && PromiseUtils.defer) {
		//need import of PromiseUtils.jsm for example: Cu.import('resource:/gree/modules/PromiseUtils.jsm');
		return PromiseUtils.defer();
	} else {
		/* A method to resolve the associated Promise with the value passed.
		 * If the promise is already settled it does nothing.
		 *
		 * @param {anything} value : This value is used to resolve the promise
		 * If the value is a Promise then the associated promise assumes the state
		 * of Promise passed as value.
		 */
		this.resolve = null;

		/* A method to reject the assocaited Promise with the value passed.
		 * If the promise is already settled it does nothing.
		 *
		 * @param {anything} reason: The reason for the rejection of the Promise.
		 * Generally its an Error object. If however a Promise is passed, then the Promise
		 * itself will be the reason for rejection no matter the state of the Promise.
		 */
		this.reject = null;

		/* A newly created Promise object.
		 * Initially in pending state.
		 */
		this.promise = new Promise(function(resolve, reject) {
			this.resolve = resolve;
			this.reject = reject;
		}.bind(this));
		Object.freeze(this);
	}
};

function resultSetToArray(sqlRS){
  var arrayRetorno = [];
  for (var i = 0, len = sqlRS.length; i < len; i++) {
     arrayRetorno.push(sqlRS.item(i));
  }

  return arrayRetorno;
};

function preDOM2DOM($contenedor, listaDOM){
    /*Función que recibe un contenedor donde buscar elementos DOM, una lista con sus respectivos nombres de id y los objetos en que se convertirán, la
        lista debe estar en el orden adecuado para que se asigne automáticamente. 
      Devuelve el DOM.*/
    var DOM = {}, preDOM, cadenaFind = "", numeroDOMs = listaDOM.length,
        tmpEntries = [], tmpObjectName = [];

    for (var i = numeroDOMs - 1; i >= 0; i--) {
        tmpEntries = Object.entries(listaDOM[i]);
        cadenaFind += (tmpEntries[0][1]+",");
        tmpObjectName[i] = tmpEntries[0][0];
    };

    cadenaFind = cadenaFind.substr(0,cadenaFind.length-1);

    preDOM = $contenedor.find(cadenaFind);

    for (var i = numeroDOMs - 1; i >= 0; i--) {
       DOM[tmpObjectName[i]] = preDOM.eq(i);
    };

    return DOM;
};

$.whenAll = function (deferreds) {
    function isPromise(fn) {
        return fn && typeof fn.then === 'function' &&
          String($.Deferred().then) === String(fn.then);
    }
    var d = $.Deferred(),
        keys = Object.keys(deferreds),
        args = keys.map(function (k) {
            return $.Deferred(function (d) {
                var fn = deferreds[k];

                (isPromise(fn) ? fn : $.Deferred(fn))
                    .done(d.resolve)
                    .fail(function (err) { d.reject(err, k); })
                ;
            });
        });

    $.when.apply(this, args)
        .done(function () {
            var resObj = {},
                resArgs = Array.prototype.slice.call(arguments);
            resArgs.forEach(function (v, i) { resObj[keys[i]] = v; });
            d.resolve(resObj);
        })
        .fail(d.reject);

    return d;
};