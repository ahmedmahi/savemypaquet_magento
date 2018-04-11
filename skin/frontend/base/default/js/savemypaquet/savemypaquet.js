/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
Varien.Savemypaquet = Class.create();
Varien.Savemypaquet.prototype = {
  initialize: function(ajaxurl, skinUrl) {
    this.ajaxurl = ajaxurl;
    this.ajaxloader = skinUrl + 'loader.gif';
    this.logo = skinUrl + 'logo-savemypaquet.png';
    this.portlogo = skinUrl + 'smp_porte.png';
    this.methodradioButton = 's_method_savemypaquet_';
    this.divconteneur = 'savemypaquet_form';
    this.smp_buttons = '';
  },
  AddSmpHtml: function() {
    this.smp_buttons = $$("input[id*='" + this.methodradioButton + "']");
    if (this.smp_buttons.length != 0) {
      var self = this;
      this.smp_buttons.each(function(_method) {
        self.method = $(_method).up('ul');
        return;
      });
      this.AddDivLogo();
      this.AddPopupEven();
      this.AddSmpDivConteneur();
      this.addButtonEven(this);
    }
  },
  AddDivLogo: function() {
    divelemtLogo = '<div class="popup" ><div class="popuptext" id="smp_popup"><img src="' + this.portlogo + '"  /><span>Votre colis est sécurisé dans un filet métalique, accroché à votre porte</span></div></div> <div id ="smp_title" class="smp_title"><div class="logo_smp" id ="logo_smp"><img src="' + this.logo + '"  /> </div><span>Livraison à domicile en votre absence, sécurisée contre le vol <br>  (système breveté) En 48h avec preuve de livraison <a  href="www.savemypaquet.com">www.savemypaquet.com</a></span></div>';
    titre = this.method.up('dd').previous('dt');
    titre.update( /*titre.innerHTML +*/ divelemtLogo);
  },
  AddSmpDivConteneur: function() {
    divelemt = '<li><span id="savemypaquet_please_wait" class="please-wait" style="display:none;"><img src="' + this.ajaxloader + '" class="v-middle" /> Chargement...</span><div id="' + this.divconteneur + '"></div></li>';
    this.method.update(this.method.innerHTML + divelemt);
    $(this.divconteneur).hide();
    //this.method.innerHTML;
  },
  addButtonEven: function(thisclass) {
    $$("input[id*='s_method_']").each(function(_method) {
      $(_method).observe('change', function(event) {
        thisclass.savemypaquetCheck(_method);
      });
    });
  },
  addFormButtonsEven: function() {
    radiogroup = new Array('digicode', 'digicode2', 'porte_position', 'interphone');
    radiogroup.each(function(item) {
      $(item).hide();
      $$("input[id*='" + item + "-']").each(function(smp_radio) {
        $(smp_radio).observe('change', function(event) {
          if (smp_radio.id == item + '-1') {
            $(item).show();
          } else {
            $(item).hide();
          }
        });
      });
    });
    $('button_smp').observe('clique', function(event) {
      this.ajaxForm(true);
    });
  },
  AddPopupEven: function() {
    $('smp_title').observe('mouseenter', function(event) {
      $('smp_popup').classList.add("show");
    });
    $('smp_title').observe('mouseleave', function(event) {
      $('smp_popup').classList.remove("show");
    });
  },
  savemypaquetCheck: function(clique) {
    $(this.divconteneur).hide();
    if (this.smp_buttons.length != 0) {
      var self = this;
      this.smp_buttons.each(function(_method) {
        if (_method.id == clique.id) {
          $(self.divconteneur).show();
          self.getForm(_method.value);
        }
      });
    }
  },
  getForm: function(option) {
    this.ajaxForm(false, option);
  },
  ajaxForm: function(envoi_form = false, option = '') {
    param = (envoi_form) ? $('co-shipping-method-form').serialize(true) : 'option=' + option;
    please_wait = (envoi_form) ? 'savemypaquet_validation_please_wait' : 'savemypaquet_please_wait';
    $(please_wait).show();
    thisclass = this;
    new Ajax.Request(this.ajaxurl, {
      method: 'post',
      onSuccess: function(transport) {
        var response = transport.responseText || "no response text";
        if (envoi_form) {
          $('savemypaquet_info').update(response);
        } else {
          $(thisclass.divconteneur).update(response);
        }
        $(please_wait).hide()
      },
      onFailure: function() {
        alert('Something went wrong...');
      },
      parameters: param,
    });
    if (envoi_form) return false;
  },
}