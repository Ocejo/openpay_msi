var app = new Vue({
  el: "#app_page",
  data: {
    isDisabled: false,
    terminosCondiciones:false,
    btnText:'Pagar',
    totalText:'$0',
    total:0,
    plans:[],
    products:[],
    emailError:false,
    customer: {
      name: "",
      lastName:"",
      email: "",
      phone: "",
    },
    card:{
      numCard: "",
      cardHoldName: "",
      expDateMonth: "",
      expDateYear: "",
      csv: "",
      plan: '',
    },
    months:[],
    years:[]
  },
  methods: {
    payment: function () {
      this.btnPagar('p');
      if(!this.valideteForm().validate){
        this.btnPagar('c');
        Swal.fire("Error", this.valideteForm().message, "error");
        return true;
      }
      OpenPay.token.create(
        {
          card_number: this.card.numCard,
          holder_name: this.card.cardHoldName,
          expiration_year: this.card.expDateYear,
          expiration_month: this.card.expDateMonth,
          cvv2: this.card.csv,
        },
        this.onSuccess,
        this.onError
      );
    },
    onSuccess: function (response) {
      this.btnPagar('p');
      const that = this;
      let post_data = {
        token: response.data.id,
        device_session_id: $("#deviceIdHiddenFieldName").val(),
        plan: that.card.plan,
        mount: that.total,
        customer: that.customer,
        security: (this.total > 5000) ? true : false,
      };
      this.$http.post("./app/Pago.php", JSON.stringify(post_data)).then(
        function (response) {
          that.btnPagar('c');
          if(response.body.success){
            let timerInterval
            Swal.fire({
              title: 'Procesando Transacción',
              html: 'Cargando...',
              timer: 2000,
              timerProgressBar: false,
              didOpen: () => {
                Swal.showLoading()
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                  b.textContent = Swal.getTimerLeft()
                }, 100)
              },
              willClose: () => {
                clearInterval(timerInterval);
                window.location.href = response.body.url;
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                console.log('I was closed by the timer')
              }
            })
          }else{
            Swal.fire("Error", response.body.message, "error");
          }
        },
        function (err) {
          this.btnPagar('c');
          console.error(err);
          Swal.fire("Error", err.body, "error");
        }
      );
    },
    onError: function (response) {
      this.btnPagar('c');
      Swal.fire("Error", response.data.description, "error");
      console.error(response.data);
    },
    emailValidate: function(){
      var that = this; 
      var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      var regOficial = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
      if (reg.test(this.customer.email) && regOficial.test(this.customer.email)) {
        that.emailError = false;
        that.isDisabled = false;
      } else if (reg.test(this.customer.email)) {
        that.emailError = false;
        that.isDisabled = false;
      } else {
        that.emailError = true;
        that.isDisabled = true;
      }
    },
    valideteForm: function () {
      var validate = true;
      var message  = "";
      if (!this.terminosCondiciones) {
        validate = false;
        message  = 'Para continuar debes aceptar los términos y condiciones';
      }
      return {
        validate: validate,
        message: message,
      };
    },
    btnPagar: function (status) {
      const that = this,
            btnStatus = {
              c: function() {
                that.isDisabled = false;
                that.btnText = 'Pagar';
              },
              p: function() {
                that.isDisabled = true;
                that.btnText = 'Pagando...';
              }
            };
      btnStatus[status]();
    },
    descript: function(str){
      switch(str) {
      case '4e07408562bedb8b60ce05c1decfe3ad16b72230967de01f640b7e4729b49fce':
          return 3
          break;
      case 'e7f6c011776e8db7cd330b54174fd76f7d0216b612387a5ffcfb81e6f0919683':
          return 6
          break;
      case '19581e27de7ced00ff1ce50b2047e7a567c76b1cbaebabe5ef03f7c3017bb5b7':
          return 9
          break;
      case '6b51d431df5d7f141cbececcf79edf3dd861c3b4069f0b11661a3eefacbba918':
          return 12
          break;
      }
    }
  },
  created: function () {
    const params = new URLSearchParams(location.search),
        montos = params.get('mount'),
        ref = params.get('ref'),
        products = params.get('product'),
        plans = params.get('plans'),
        that = this;
    if(montos==null||products==null||plans==null){
      Swal.fire("Error", 'Parametros necesarios', "error");
      that.btnPagar('c');
    }else{
      let plansArr    = plans.split(','),
          productsArr = products.split(','),
          mountsArr   = montos.split(',');
      plansArr.forEach((plan, idx) => {
          if (idx == 0) that.card.plan = that.descript(plan);
          that.plans.push({text:that.descript(plan)+' Meses', value:that.descript(plan)});
      });
      productsArr.forEach((product, idx) => {
          that.total +=  parseFloat(mountsArr[idx]);
          that.products.push({name: product, mount: Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(mountsArr[idx]) + ' MXN'});
      });
      this.totalText = Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(this.total) + ' MXN';
    }
    let months = ['01','02','03','04','05','06','07','08','09','10','11','12'];
    for (let index = 0; index < 10; index++) {
      let arrYear = {
          text: new Date().getFullYear() + index,
          value: parseFloat(new Date().getFullYear().toString().substr(-2)) + index
      };
      that.years.push(arrYear);
    }
    months.forEach((value,index)=>{
      let arrMonth = {
          text: value,
          value: value,
      };
      that.months.push(arrMonth);
    });
    console.log(this.months);
  },
  mounted: function () {},
});

$(document).ready(function () {
  OpenPay.setId("mmey69zmnv0ub7zhny23");
  OpenPay.setApiKey("pk_234a0463f2a94c459334aea543092455");
  OpenPay.setSandboxMode(true);
  var deviceSessionId = OpenPay.deviceData.setup(
    "payment-form",
    "deviceIdHiddenFieldName"
  );
});
