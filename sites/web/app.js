var Stock = Stock || {};

(function($) {
    
    $(document).ready(function() {
        $('#stock-symbol').click(function(event) {
            event.stopPropagation();
            event.preventDefault();
        });
    });
    
})(window.jQuery);

(function(Backbone, $, _, Stock) {
    'use strict';
    
    Stock.ProfileWidgetContent = Backbone.View.extend({
        title: function() {
            return 'STOCK PROFILE';
        },
        size: function() {
            return [1, 1, 2, 1];
        },
        render: function() {
            var func = _.template(this.template);
            return func(this.model);
        },
        template: '<div class="row-fluid"><div class="span3 fx-large column decolumn_mini"><a class="nounderline"><%= symbol %></a></div>\n\
                <div class="span9"><div class="clearfix decolumn_mini">Stock Name: <%= name %></div>\n\
                <div class="clearfix decolumn_mini">Stock Industry: <%= industry %></div>\n\
                <div class="clearfix decolumn_mini">Stock Exchange: <%= exchange %></div>\n\
                <div class="raphael-ui-component flat-ui-font clearfix decolumn_mini">Currency: <%= currency %></div></div>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.PriceWidgetContent = Backbone.View.extend({
        title: function() {
            return 'STOCK PRICE';
        },
        size: function() {
            return [1, 3, 1, 1];
        },
        render: function() {
            var func = _.template(this.template);
            return func(this.model);
        },
        template: '<span class="fx-large clearfix decolumn_mini"><%= price %></span>\n\
                    <span class="raphael-ui-component clearfix decolumn_mini"><%= price_change %></span>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.VolumeWidgetContent = Backbone.View.extend({
        title: function() {
            return 'STOCK VOLUME';
        },
        size: function() {
            return [1, 4, 1, 1];
        },
        render: function() {
            var func = _.template(this.template);
            return func(this.model);
        },
        template: '<span class="fx-large clearfix decolumn_mini"><%= volume %></span>\n\
                    <span class="raphael-ui-component clearfix decolumn_mini"><%= volume_change %></span>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.IndustryRankWidgetContent = Backbone.View.extend({
        title: function() {
            return 'INDUSTRY RANK';
        },
        size: function() {
            return [1, 5, 1, 1];
        },
        render: function() {
            var func = _.template(this.template);
            return func(this.model);
        },
        template: '<span class="fx-large clearfix decolumn_mini"><%= industry_rank %></span>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.MarketRankWidgetContent = Backbone.View.extend({
        title: function() {
            return 'MARKET RANK';
        },
        size: function() {
            return [1, 6, 1, 1];
        },
        render: function() {
            var func = _.template(this.template);
            return func(this.model);
        },
        template: '<span class="fx-large clearfix decolumn_mini"><%= market_rank %></span>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.ReturnWidgetContent = Backbone.View.extend({
        title: function() {
            return 'STOCK RETURN';
        },
        size: function() {
            return [2, 1, 1, 2];
        },
        render: function() {
            var func = _.template(this.template);
            return func({
                week: this.model['week'] ? this.model['week'].price : null,
                quarter: this.model['quarter'] ? this.model['quarter'].price : null,
                half: this.model['half'] ? this.model['half'].price : null,
                year: this.model['year'] ? this.model['year'].price : null,
            });
        },
        template: '<span class="fx-large clearfix decolumn_mini"><%= week %></span>\n\
                <span class="fx-large clearfix decolumn_mini"><%= quarter %></span>\n\
                <span class="fx-large clearfix decolumn_mini"><%= half %></span>\n\
                <span class="fx-large clearfix decolumn_mini"><%= year %></span>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.InvestorWidgetContent = Backbone.View.extend({
        title: function() {
            return 'STOCK INVESTORS';
        },
        size: function() {
            return [2, 2, 4, 1];
        },
        render: function() {
            var func = _.template(this.template);
            return func(this.model);
        },
        template: '<span class="raphael-ui-component"><%= type1 %></span>\n\
            <span class="raphael-ui-component"><%= type2 %></span>\n\
            <span class="raphael-ui-component"><%= type3 %></span>\n\
            <span class="raphael-ui-component"><%= type4 %></span>\n\
            <span class="raphael-ui-component"><%= type5 %></span>\n\
            <span class="raphael-ui-component"><%= type6 %></span>\n\
            <span class="raphael-ui-component"><%= type7 %></span>\n\
            <span class="raphael-ui-component"><%= type8 %></span>\n\
            <span class="raphael-ui-component"><%= type9 %></span>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.NewsWidgetContent = Backbone.View.extend({
        title: function() {
            return 'STOCK NEWS';
        },
        size: function() {
            return [3, 2, 2, 2];
        },
        render: function() {
            var func = _.template(this.template);
            return func(this.mdoel);
        },
        template: '<span class="fx-large clearfix"><%= market_rank %></span>',
        initialize: function(options) {
            return this;
        }
    });
    
    Stock.WidgetView = Backbone.View.extend({
        tagName: 'ul',
        className: 'gridster-container white span12',
        maxColumn: 6,
        widgets: function() {
            return [
                {'price':'PriceWidgetContent'},{'volume':'VolumeWidgetContent'},
                {'industry':'IndustryRankWidgetContent'},{'market':'MarketRankWidgetContent'},
                {'profile':'ProfileWidgetContent'},{'return':'ReturnWidgetContent'},
                {'news':'NewsWidgetContent'},{'investor': 'InvestorWidgetContent'}
            ];
        },
        render: function() {
            var func = null;
            var view = null;
            var widgetHtml = null;
            var size = null;
            var class_name = null;
            var positions = null;
            var views = _.values(this.collection.views);
            var widgetTypes = _.keys(this.collection.views);
            _.each(views, function(element, index) {
                if(element.view && element.model) {
                    func = _.template($('script#widget-view').html());
                    view = new element.view({
                        model: element.model
                    });
                    size = view.size();
                    class_name = this.calculateDimensions(size);
                    class_name = class_name.join(' ');
                    widgetHtml = func({
                        widget_row: size[0],
                        widget_col: size[1],
                        widget_sizex: size[2],
                        widget_sizey: size[3],
                        widget_title: view.title(),
                        widget_content: view.render(),
                        class_name: class_name
                    });
                    $(this.el).append(widgetHtml);
                }
            }, this);
            this.setContent();
        },
        calculateDimensions: function(size) {
            var x = size[2];
            var y = size[3];
            
            var class_name = [];
            
            switch(x) {
                case 1:
                    class_name.push('span2');
                    break;
                case 2:
                    class_name.push('span5');
                    break;
                case 3:
                    class_name.push('span7');
                    break;
                case 4:
                    class_name.push('span10');
                    break;
                default: break;
            }
            
            switch(y) {
                case 1:
                    class_name.push('vertical-span2');
                    break;
                case 2:
                    class_name.push('vertical-span5');
                    break;
                case 3:
                    class_name.push('vertical-span7');
                    break;
                case 4:
                    class_name.push('vertical-span10');
                    break;
                default: break;
            }
            
            return class_name;
        },
        initialize: function() {
            _.each(this.widgets(), this.collection.addView, this.collection);
            return this;
        },
        setContent: function() {
            $('#page-gridster').append(this.el);
            
            var verticalSpan = $(this.el).find('.vertical-span2').first();
            var horizontalSpan = $(this.el).find('.span2').first();
            var $widget_margin_horiz = ( verticalSpan.outerHeight(true) - verticalSpan.outerHeight() ) / 2;
            var $widget_base_width = horizontalSpan.width();
            var $widget_base_height = horizontalSpan.height();
            
            if(!!$.fn.gridster) {
                $(this.el).gridster({
                    widget_margins: [$widget_margin_horiz, $widget_margin_horiz],
                    widget_base_dimensions: [$widget_base_width, $widget_base_height]
                });
            }
        }
    });
    
    Stock.WidgetCollection = Backbone.Collection.extend({
        initialize: function() {
            this.views = {};
        },
        addView: function(element, index) {
            var values = _.values(element);
            var keys = _.keys(element);
            this.views[keys[0]] = {'view': Stock[values[0]]};
        },
        addItem: function(element, index) {
            var model = element.toJSON();
            var values = _.values(model);
            var keys = _.keys(model);
            this.views[keys[0]]['model'] = values[0];
        },
        getViews: function() {
            return this.views;
        }
    });
    
    Stock.DashboardView = Backbone.View.extend({
        widgetView: new Stock.WidgetView({
            collection: new Stock.WidgetCollection()
        }),
        initialize: function(options) {
            this.options = options;
            return this;
        },
        render: function() {
            this.collection.url = this.collection.url.replace('{symbol}/{id}', this.model.symbol() + '/' + this.model.get('id'));
            this.execute();
        },
        execute: function() {
            var view = this;
            var callbacks = execute.apply(this);
            var success = callbacks.success;
            var promise = _.extend({}, callbacks);
            promise.success = function(collection, response) {
                success(collection, response);
                view.loadResult(collection);
            };
            this.collection.fetch(promise);
        },
        loadResult: function(collection) {
            if (_.size(collection)) {
                collection.each(this.widgetView.collection.addItem, this.widgetView.collection);
            }
            this.widgetView.render();
        }
    });
    
    var Collection = {},
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
            $(document).on('click.symbol', function ( e ) {
                that.hide()
            });
        } else if (!this.isShown) {
            $(document).off('keyup');
            $(document).off('click.symbol');
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
        className: 'alert alert-info clearfix column_small decolumn_small',
        template: '<button type="button" class="close" data-dismiss="alert">&times;</button><strong><%= type %>!</strong> <%= message %>',
        render: function(options) {
            var func = _.template(this.template);
            return func(options);
        }
    }),
    
    BootstrapAlertPreLoaded = Backbone.View.extend({
        initialize: function(options) {
            var view = this;
            $(this.el).html(options.message);
            return this;
        },
        tagName: 'div',
        className: 'clearfix'
    }),
    
    common = {
        error: function(message) {
            var bootstrapAlert = new BootstrapAlert({
                message : message ? message : 'Request Could not be satisfied',
                type: 'Information'
            });
            $('.status-messages').html(bootstrapAlert.el).show();
        },
        errorPreloaded: function(message) {
            var bootstrapAlert = new BootstrapAlertPreLoaded({
                message : message
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
                if(!xhr.responseJSON) {
                    common.error();
                } else {
                    Collection.loadError(xhr);
                }
                return this;
            },
            complete: function(xhr, textStatus) {
                
            }
        };
        return object;
    };
            
    Workspace.AutoCompleteItemView = Backbone.View.extend({
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

    Workspace.StockChoice = Backbone.Model.extend({
        symbol: function() {
            return this.get("symbol");
        },
        label: function() {
            return this.symbol();
        }
    });
    
    Workspace.StockChoiceList = Backbone.Collection.extend({
        model: Workspace.StockChoice,
        url: "/symbol",
        initialize: function(options) {
            this.fetched = 0;
            return this;
        }
    });
    
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
        if(!collection) {
            common.error();
        }
        return this;
    };
    
    Collection.loadError = function(responseObject) {
        if(_.size(responseObject.responseJSON)) {
            common.errorPreloaded(responseObject.responseJSON[0].data);
        } else if(responseObject.responseText) {
            var json = JSON.parse(responseObject.responseText);
            common.errorPreloaded(json[0].data);
        }
    };
    
//    Stock.toQueryString = function(obj) {
//      var parts = [];      
//      for(var each in obj) if (obj.hasOwnProperty(each)) {  
//        parts.push(encodeURIComponent(each) + '=' + encodeURIComponent(obj[each]));      
//      }      
//      return Stock.BASE_URI + parts.join('&');    
//    };
//    
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
    
    Stock.StockSymbolData = Backbone.Collection.extend({
        url: "/stock/{symbol}/{id}",
        initialize: function() {
            
        }
    });
    
    Stock.StockSymbolInput = Backbone.Model.extend({
        stock_symbol: null,
        changeStockSymbol: function(model, view, dashboard) {
            var attr = $(view.el).data('action');
            var value = model.symbol() + '/' + model.get('id');
            $(view.el).attr('action', attr[attr.length - 1] === '/' ? attr + value : attr + '/' + value);
            $('input[name="stock_id"]').val(model.get('id'));
            new dashboard({
                collection: new Stock.StockSymbolData(),
                model: model
            }).render();
        },
        initialize: function() {
            this.on('change:stock_symbol', function(model, value, options) {
                model.changeStockSymbol(value, options.view, options.dashboard);
            });
        }
    });
    
    Workspace.RaphaelDOMWrapper = Backbone.View.extend({
        initialize: function(options) {
            this.raphaelDOM = options.dom;
            $(this.el).append(this.raphaelDOM);
            return this;
        }
    });
    
    (function(Backbone, $, _) {
        var stockChoiceList = new Workspace.StockChoiceList();
        var stockInputModel = new Stock.StockSymbolInput();
        
        Stock.StockInputFormView = Backbone.View.extend({
            model: stockInputModel,
            initialize: function(options) {
                this.input = $(this.el).find('#stock-symbol');
            }
        });
        
        Workspace.AutoCompleteView = Backbone.View.extend({
            tagName: "ul",
            className: "autocomplete btn-success",
            wait: 300,
            queryParameter: "symbol",
            minKeywordLength: 1,
            currentText: "",
            itemView: Workspace.AutoCompleteItemView,
            formView: new Stock.StockInputFormView({
                el: $('#stock-symbol').parents('form.form-search').first()
            }),
            dashboardView: Stock.DashboardView,

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
                        view.loadResult(collection, keyword);
                    };
                    promise.data = parameters;
                    Backbone.ajax(_.extend({
                        'type': 'POST',
                        'url': this.model.url
                    }, promise));
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
                if (model && model.length) {
                    _.forEach(model, this.addItem, this);
                    this.show();
                } else {
                    this.hide();
                    common.error();
                }
            },

            addItem: function (model) {
                this.$el.append(new this.itemView({
                    model: new Workspace.StockChoice(model),
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
                    view: this.formView,
                    dashboard: this.dashboardView
                });
            }

        });
        
        new Workspace.AutoCompleteView({
            input: $("#stock-symbol"),
            model: stockChoiceList,
            modelBind: stockInputModel
        }).render();
    })(window.Backbone, window.jQuery, window._);
    
})(window.Backbone, window.jQuery, window._, Stock);