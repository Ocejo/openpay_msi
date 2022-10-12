var app = new Vue({
    el: "#app_page",
    data: {
        title:'PAGO ',
        description:'',
        src:'./images/',
        loading: true,
        contador:0,
    },
    methods: {
        verificarPago:function(){
            this.loadingFt();
        },
        loadingFt:function(){
            this.contador++;
            const that = this;
            if (this.contador<4) {
                var timerInterval;
                const params = new URLSearchParams(location.search),
                id_transaccion = params.get('id');
                if(id_transaccion==''||id_transaccion==null){
                    Swal.fire("Upss", 'Identificar no reconocido', "question");
                    return true;
                }
                that.loading = true;
                Swal.fire({
                    title: 'Verificando la Transacción',
                    html: 'Esto puede tardar unos minutos...',
                    timer: 5000,
                    timerProgressBar: false,
                    didOpen: () => {
                        Swal.showLoading()
                        const b = Swal.getHtmlContainer().querySelector('b')
                        timerInterval = setInterval(() => {
                        b.textContent = Swal.getTimerLeft()
                        }, 100)
                    },
                    willClose: () => {
                        let post_data = {
                            id:id_transaccion
                        }
                        that.$http.post("./app/Transaccion.php", JSON.stringify(post_data)).then(
                            function (res) {
                                if(res.body.code == '2811') {
                                    that.loadingFt();
                                    return true
                                }
                                that.description = res.body.message;
                                that.src += (res.body.success) ? 'true.svg' : 'false.svg';
                                that.title += (res.body.success) ? 'EXITOSO' : 'DECLINADO';
                            },
                            function (err) {
                                console.error(err);
                                Swal.fire("Error", err.body.message, "error");
                            }
                        );
                        clearInterval(timerInterval);
                        that.loading = false;
                    }
                    
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                    }
                });
            }else{
                that.description = 'Tu pago aun no ha sido procesado';
                that.src += 'false.svg';
                that.title += 'DECLINADO';
            }
            
        }
    },
    created: function () {
        this.verificarPago();
    },

  });
  