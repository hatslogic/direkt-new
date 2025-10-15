import deDE from '../../snippet/de-DE.json';
import enGB from '../../snippet/en-GB.json';
import template from './dashboard.twig';
import styles from './dashboard.scss';

Shopware.Component.register('nimbits-tracking-dashboard', {
    template,
    styles,

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        }
    },

    data() {
        const me = this;
        const now = new Date();
        const monthAgo = new Date();
        monthAgo.setMonth(monthAgo.getMonth() - 1);

        const flatpickrConfig = {
            altInput: true,
            altFormat: me.$tc('nimbits-tracking.date.format'),
            dateFormat: "Y-m-d"
        }

        return {
            timeoutId: null,

            exportCsvRunning: false,

            download: {
                content: null,
                name: null
            },

            title: {
                exportCsv: me.$tc('nimbits-tracking.navigation.button.export'),
                reload: me.$tc('nimbits-tracking.navigation.button.reload')
            },

            dates: {
                end: now.toISOString(),
                start: monthAgo.toISOString(),
                config: flatpickrConfig
            },

            loading: {
                registrations: true,
                orders: true,
                sales: true,
                visitors: true,
                requests: true
            },

            registrationsByPartnerOptions: {
                title: {
                    text: me.$tc('nimbits-tracking.charts.registrations.options.header')
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + " " + me.$tc('nimbits-tracking.charts.registrations.options.tooltip');
                        }
                    }
                },
                legend: {
                    formatter: function (seriesName, opts) {
                        if (seriesName.length > 0) {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.registrations.options.legend.content'), " ", seriesName];
                        } else {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.registrations.options.legend.empty')];
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    formatter: function (val) {
                                        if (val === me.$tc('nimbits-tracking.charts.registrations.options.legend.total')) {
                                            return val;
                                        } else if (val.length > 0) {
                                            return me.$tc('nimbits-tracking.charts.registrations.options.legend.content') + " " + val;
                                        } else {
                                            return me.$tc('nimbits-tracking.charts.registrations.options.legend.empty');
                                        }
                                    }
                                },
                                total: {
                                    show: true,
                                    label: me.$tc('nimbits-tracking.charts.registrations.options.legend.total'),
                                    color: '#52667a'
                                }
                            }
                        }
                    }
                }
            },

            registrationsByPartnerSeries: [{
                data: []
            }],

            ordersByPartnerOptions: {
                title: {
                    text: me.$tc('nimbits-tracking.charts.orders.options.header')
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + " " + me.$tc('nimbits-tracking.charts.orders.options.tooltip');
                        }
                    }
                },
                legend: {
                    formatter: function (seriesName, opts) {
                        if (seriesName.length > 0) {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.orders.options.legend.content'), " ", seriesName];
                        } else {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.orders.options.legend.empty')];
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    formatter: function (val) {
                                        if (val === me.$tc('nimbits-tracking.charts.orders.options.legend.total')) {
                                            return val;
                                        } else if (val.length > 0) {
                                            return me.$tc('nimbits-tracking.charts.orders.options.legend.content') + " " + val;
                                        } else {
                                            return me.$tc('nimbits-tracking.charts.orders.options.legend.empty');
                                        }
                                    }
                                },
                                total: {
                                    show: true,
                                    label: me.$tc('nimbits-tracking.charts.orders.options.legend.total'),
                                    color: '#52667a'
                                }
                            }
                        }
                    }
                }
            },

            ordersByPartnerSeries: [{
                data: []
            }],

            salesByPartnerOptions: {
                title: {
                    text: me.$tc('nimbits-tracking.charts.sales.options.header')
                },
                plotOptions: {
                    bar: {
                        distributed: true
                    },
                    dataLabels: {
                        position: 'center'
                    }
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return Shopware.Utils.format.currency(Math.round((val + Number.EPSILON) * 100) / 100, Shopware.Context.app.systemCurrencyISOCode);
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return Shopware.Utils.format.currency(Math.round((val + Number.EPSILON) * 100) / 100, Shopware.Context.app.systemCurrencyISOCode);
                        }
                    }
                },
                xaxis: {
                    labels: {
                        formatter: function (val) {
                            if (val.length > 0) {
                                return val;
                            } else {
                                return me.$tc('nimbits-tracking.charts.sales.noreferrer');
                            }
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return val.toFixed(0);
                        }
                    }
                }
            },

            salesByPartnerSeries: [{
                name: me.$tc('nimbits-tracking.charts.sales.series.name'),
                data: []
            }],

            visitorsByPartnerOptions: {
                title: {
                    text: me.$tc('nimbits-tracking.charts.visitors.options.header')
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + " " + me.$tc('nimbits-tracking.charts.visitors.options.tooltip');
                        }
                    }
                },
                legend: {
                    formatter: function (seriesName, opts) {
                        if (seriesName.length > 0) {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.visitors.options.legend.content'), " ", seriesName];
                        } else {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.visitors.options.legend.empty')];
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    formatter: function (val) {
                                        if (val === me.$tc('nimbits-tracking.charts.visitors.options.legend.total')) {
                                            return val;
                                        } else if (val.length > 0) {
                                            return me.$tc('nimbits-tracking.charts.visitors.options.legend.content') + " " + val;
                                        } else {
                                            return me.$tc('nimbits-tracking.charts.visitors.options.legend.empty');
                                        }
                                    }
                                },
                                total: {
                                    show: true,
                                    label: me.$tc('nimbits-tracking.charts.visitors.options.legend.total'),
                                    color: '#52667a'
                                }
                            }
                        }
                    }
                }
            },

            visitorsByPartnerSeries: [{
                data: []
            }],

            requestsByPartnerOptions: {
                title: {
                    text: me.$tc('nimbits-tracking.charts.requests.options.header')
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + " " + me.$tc('nimbits-tracking.charts.requests.options.tooltip');
                        }
                    }
                },
                legend: {
                    formatter: function (seriesName, opts) {
                        if (seriesName.length > 0) {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.requests.options.legend.content'), " ", seriesName];
                        } else {
                            return [opts.w.globals.series[opts.seriesIndex], " ", me.$tc('nimbits-tracking.charts.requests.options.legend.empty')];
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    formatter: function (val) {
                                        if (val === me.$tc('nimbits-tracking.charts.requests.options.legend.total')) {
                                            return val;
                                        } else if (val.length > 0) {
                                            return me.$tc('nimbits-tracking.charts.requests.options.legend.content') + " " + val;
                                        } else {
                                            return me.$tc('nimbits-tracking.charts.requests.options.legend.empty');
                                        }
                                    }
                                },
                                total: {
                                    show: true,
                                    label: me.$tc('nimbits-tracking.charts.requests.options.legend.total'),
                                    color: '#52667a'
                                }
                            }
                        }
                    }
                }
            },

            requestsByPartnerSeries: [{
                data: []
            }],

            httpClient: Shopware.Application.getContainer('init').httpClient
        }
    },

    computed: {
        datePickerStartDate() {
            return this.dates.start;
        },

        datePickerEndDate() {
            return this.dates.end;
        },
    },

    watch: {
        datePickerStartDate() {
            this.getData();
        },

        datePickerEndDate() {
            this.getData();
        }
    },

    methods: {
        async exportCSV() {
            this.exportCsvRunning = true;
            this.httpClient.post('nimbits/tracking/export-csv', {
                startDate: this.dates.start,
                endDate: this.dates.end
            }, {
                headers: {
                    Authorization: `Bearer ${Shopware.Context.api.authToken.access}`
                }
            }).then((response) => {
                if (response.status === 200) {
                    this.downloadReceivedFile(response);
                }

                this.exportCsvRunning = false;
            });
        },

        downloadReceivedFile(response) {
            const disposition = response.headers['content-disposition'];
            if (disposition && disposition.indexOf('attachment') !== -1) {
                const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                const matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    this.download.name = matches[1].replace(/['"]/g, '');
                }
            }

            this.download.content = 'data:text/csv;charset=utf-8,' + encodeURIComponent(response.data);
            setTimeout(() => document.getElementById('nb-tracking-download').click(), 100);
        },

        async getData() {
            if (this.timeoutId !== null) {
                window.clearTimeout(this.timeoutId);
            }

            this.httpClient.post('nimbits/tracking/registrations', {
                startDate: this.dates.start,
                endDate: this.dates.end
            }, {
                headers: {
                    Authorization: `Bearer ${Shopware.Context.api.authToken.access}`
                }
            }).then((response) => {
                if (response.status === 200) {
                    this.registrationsByPartnerSeries[0].data = response.data.result.registrations;
                    this.loading.registrations = false;
                }
            });

            this.httpClient.post('nimbits/tracking/orders', {
                startDate: this.dates.start,
                endDate: this.dates.end
            }, {
                headers: {
                    Authorization: `Bearer ${Shopware.Context.api.authToken.access}`
                }
            }).then((response) => {
                if (response.status === 200) {
                    this.salesByPartnerSeries[0].data = response.data.result.sales;
                    this.loading.sales = false;

                    this.ordersByPartnerSeries[0].data = response.data.result.orders;
                    this.loading.orders = false;
                }
            });

            this.httpClient.post('nimbits/tracking/visitors', {
                startDate: this.dates.start,
                endDate: this.dates.end
            }, {
                headers: {
                    Authorization: `Bearer ${Shopware.Context.api.authToken.access}`
                }
            }).then((response) => {
                if (response.status === 200) {
                    this.visitorsByPartnerSeries[0].data = response.data.result.visitors;
                    this.loading.visitors = false;

                    this.requestsByPartnerSeries[0].data = response.data.result.requests;
                    this.loading.requests = false;
                }
            });

            this.timeoutId = window.setTimeout(() => this.getData(), 60000);
        }
    },

    mounted() {
        this.getData();
    }
});