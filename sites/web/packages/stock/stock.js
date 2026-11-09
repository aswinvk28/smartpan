(function(Backbone, $, _) {
    'use strict';
    
    var Stock = Stock || {},
    Collection = {},
    Workspace = Workspace || {},
    latestStock = function(symbol) {
        return new Stock.StockData({
            url: "http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20from%20yahoo.finance.quoteslist%20where%20symbol%20%3D%20'" + symbol + "'&format=json&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback="
        });
    },
            
    escape = function(namespace) {
        var that = this;
        if (this.isShown) {
            $(document).on('keyup', function ( e ) {
                e.which == 27 && that.hide()
            });
        } else if (!this.isShown) {
            $(document).off('keyup');
        }
    },
    
    historicalSTock = function(symbol, startDate, endDate) {
        return new Stock.StockData({
            url: "http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20from%20yahoo.finance.historicaldata%20where%20symbol%20%3D%20'" + symbol + "'%20and%20startDate='" + startDate + "'%20andendDate='" + endDate + "'&format=json&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback="
        });
    },
    
    BootstrapAlert = Backbone.View.extend({
        initialize: function(options) {
            var view = this;
            $(this.el).html(view.render({
                message: options.message,
                type: options.type
            }));
            return this;
        },
        tagName: 'div',
        className: 'alert clearfix column_small decolumn_small',
        template: '<button type="button" class="close" data-dismiss="alert">&times;</button><strong><%= type %>!</strong> <%= message %>',
        render: function(options) {
            var func = _.template(this.template);
            return func(options);
        }
    }),
    
    common = {
        error: function() {
            var bootstrapAlert = new BootstrapAlert({
                message : 'Requested Resource Not Found',
                type: 'Warning'
            });
            $('.status-messages').html(bootstrapAlert.el).show();
        }
    },

    execute = function() {
        var context = this;
        var object = {
            success: function(collection, response) {
                Collection.fetchData.call(context, collection, response);
            },
            error: function(xhr, textStatus, errorThrown) {
                common.error();
                return this;
            },
            complete: function() {
                
            }
        };
        return object;
    };
    
    Stock.BASE_URI = 'http://query.yahooapis.com/v1/public/yql?';
    Stock.ENV_PARAM = "store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
    Stock.FORMAT = 'json';
    
    Stock.PARAMS = function(options) {
        var context = this;
        var object = {
            q: options.query,
            diagnostics: context.diagnostics,
            env: Stock.ENV_PARAM,
            format: Stock.FORMAT
        };
        return object;
    };
    
    Collection.fetchData = function(collection, response) {
        this.fetched++;
        if(!response) {
            this.errorPage();
        }
        return this;
    };
    
    Stock.toQueryString = function(obj) {
      var parts = [];      
      for(var each in obj) if (obj.hasOwnProperty(each)) {  
        parts.push(encodeURIComponent(each) + '=' + encodeURIComponent(obj[each]));      
      }      
      return Stock.BASE_URI + parts.join('&');    
    };
    
//    Stock.StockData = Backbone.Collection.extend({
//        url: null,
//        diagnostics: false,
//        urlGenerator: function(options) {
//            this.url = options.url || Stock.toQueryString(Stock.PARAMS.call(this, options));
//        },
//        initialize: function(options) {
//            this.urlGenerator(options);
//            this.fetched = 0;
//            this.fetch(execute.apply(this));
//            return this;
//        },
//        errorPage: common.errorPage
//    });
//    
//    Stock.StockPriceCollection = Backbone.Collection.extend({
//        sync: Backbone.localforage.sync('Stock.StockPriceCollection'),
//        initialize: function(options) {
//            
//        }
//    });
//    
//    Stock.StockVolumeCollection = Backbone.Collection.extend({
//        sync: Backbone.localforage.sync('Stock.StockVolumeCollection'),
//        initialize: function(options) {
//            
//        }
//    });
//    
//    Stock.WidgetView = Backbone.View.extend({
//        render: function(options) {
//            var func = _.template(this.template);
//            return func(options);
//        },
//        template: function() {
//            return $('#script.widget-view').html()
//        },
//        initialize: function() {
//            var view = this;
//            $(this.el).html(view.render(view.model)); /*{
//                                                            message: options.message,
//                                                            type: options.type
//                                                        }*/
//            return this;
//        }
//    });
//    
//    Stock.PriceWidgetContent = Backbone.View.extend({
//        render: function(options) {
//            var func = _.template(this.template);
//            return func(options);
//        },
//        template: function() {
//            return '<span class="fx-large clearfix"><%=price%></span><span class="raphael-ui-component clearfix"><%=price_change%></span>';
//        },
//        initialize: function(options) {
//            var view = this;
//            this.price = options.price;
//            $(this.el).html(view.render(view.model)); 
//            /*{
//                price: options.price,
//                price_change: options.price_change
//            }*/
//            return this;
//        }
//    });
    
    var StockChoice = Backbone.Model.extend({
        symbol: function() {
            return this.get("symbol");
        },
        label: function() {
            return this.symbol();
        }
    });
    
    var StockChoiceList = Backbone.Collection.extend({
        model: StockChoice,
        url: "http://smartpan.financial:9999/stock",
        initialize: function(options) {
            this.fetched = 0;
            return this;
        }
    });
    
    var stockChoiceList = new StockChoiceList();
    
    Stock.StockSymbolInput = Backbone.Model.extend({
        stock_symbol: null,
        changeStockSymbol: function(model, view) {
            var attr = $(view.el).data('action');
            var value = model.symbol() + '/' + model.get('id');
            $(view.el).attr('action', attr[attr.length - 1] === '/' ? attr + value : attr + '/' + value);
        },
        initialize: function() {
            this.on('change:stock_symbol', function(model, value, options) {
                model.changeStockSymbol(value, options.view);
            });
        }
    });
    
    var stockInputModel = new Stock.StockSymbolInput();
    
    Stock.StockInputFormView = Backbone.View.extend({
        model: stockInputModel,
        initialize: function(options) {
            this.input = $(this.el).find('#stock-symbol');
        },
        submit: function() {
            var callbacks = execute.apply(this);
            var success = callbacks.success;
            var promise = _.extend({}, callbacks);
            promise.success = function(collection, response) {
                success(collection, response);
                this.loadResult(this.colection.models);
            };
            this.collection.fetch(promise);
        },
        render: function() {
            
        },
        loadResult: function(models) {
            if (models.length) {
                _.forEach(models, this.targetElement.addItem, this);
            }
        }
    });
    
    Workspace.RaphaelDOMWrapper = Backbone.View.extend({
        initialize: function(options) {
            this.raphaelDOM = options.dom;
            $(this.el).append(this.raphaelDOM);
            return this;
        }
    });
    
    var AutoCompleteItemView = Backbone.View.extend({
        tagName: "li",
        template: _.template('<a href="#" data-stock-id="<%= id %>" data-stock-symbol="<%= symbol %>"><%= symbol %></a>'),

        events: {
            "click": "select"
        },

        initialize: function(options) {
            this.options = options;
        },

        render: function () {
            this.$el.html(this.template({
                "id": this.model.get('id'),
                "symbol": this.model.symbol()
            }));
            return this;
        },

        select: function () {
            this.options.parent.hide().select(this.model);
            return false;
        }

    });

    var AutoCompleteView = Backbone.View.extend({
        tagName: "ul",
        className: "autocomplete",
        wait: 300,
        queryParameter: "symbol",
        minKeywordLength: 1,
        currentText: "",
        itemView: AutoCompleteItemView,
        formView: new Stock.StockInputFormView({
            el: $('#stock-symbol').parents('form.form-search').first()
        }),

        initialize: function (options) {
            _.extend(this, options);
            this.filter = _.debounce(this.filter, this.wait);
        },

        render: function () {
            // disable the native auto complete functionality
            this.input.attr("autocomplete", "off");

            this.$el.width(this.input.outerWidth());

            this.input
                .keyup(_.bind(this.keyup, this))
                .keydown(_.bind(this.keydown, this))
                .after(this.$el);

            return this;
        },

        keydown: function (event) {
            if (event.keyCode == 38) return this.move(-1);
            if (event.keyCode == 40) return this.move(+1);
            if (event.keyCode == 13) return this.onEnter();
            if (event.keyCode == 27) return this.hide();
        },

        keyup: function () {
            var keyword = this.input.val();
            if (this.isChanged(keyword)) {
                if (this.isValid(keyword)) {
                    this.filter(keyword);
                } else {
                    this.hide()
                }
            }
        },

        filter: function (keyword) {
            if (this.model.url) {
                var parameters = {};
                var view = this;
                parameters[this.queryParameter] = keyword;
                var callbacks = execute.apply(this);
                var success = callbacks.success;
                var promise = _.extend({}, callbacks);
                promise.success = function(collection, response) {
                    success(collection, response);
                    view.loadResult(collection.models, keyword);
                };
                promise.data = parameters;
                console.log(this.model.sync('create', this.model, promise));
            } else {
                this.loadResult(this.model.filter(function (model) {
                    return model.label().toLowerCase().indexOf(keyword) !== -1
                }), keyword);
            }
        },

        isValid: function (keyword) {
            return keyword.length > this.minKeywordLength
        },

        isChanged: function (keyword) {
            return this.currentText != keyword;
        },

        move: function (position) {
            var current = this.$el.children(".active"),
                siblings = this.$el.children(),
                index = current.index() + position;
            if (siblings.eq(index).length) {
                current.removeClass("active");
                siblings.eq(index).addClass("active");
            }
            return false;
        },

        onEnter: function () {
            this.$el.children(".active").click();
            return false;
        },

        loadResult: function (model, keyword) {
            this.currentText = keyword;
            this.show().reset();
            if (model.length) {
                _.forEach(model, this.addItem, this);
                this.show();
            } else {
                this.hide();
            }
        },

        addItem: function (model) {
            this.$el.append(new this.itemView({
                model: model,
                parent: this
            }).render().$el);
        },

        select: function (model) {
            var label = model.label();
            this.input.val(label);
            this.currentText = label;
            this.onSelect(model);
        },

        reset: function () {
            this.$el.empty();
            return this;
        },

        hide: function () {
            this.isShown = false;
            escape.call(this);
            this.$el.hide();
            return this;
        },

        show: function () {
            this.isShown = true;
            escape.call(this);
            this.$el.show();
            return this;
        },

        // callback definitions
        onSelect: function (model) {
            var value = $(this.el).val();
            this.modelBind.set('stock_symbol', model, {
                silent: false,
                view: this.formView
            });
//            var options = {
//                symbol: value
//            };
//            var stockData = new Stock.StockData(options);
        }

    });
    
    new AutoCompleteView({
        input: $("#stock-symbol"),
        model: stockChoiceList,
        modelBind: stockInputModel
    }).render();
    
})(window.Backbone, window.jQuery, window._);