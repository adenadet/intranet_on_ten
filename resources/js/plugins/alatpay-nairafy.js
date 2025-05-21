// NairafyButtonPlugin.js
import { h, render } from 'vue';

export default {
  install(app) {
    app.component('NairafyButton', {
      props: {
        vendorId: { type: String, required: true },
        email: { type: String, required: true },
        phone_number: { type: String, required: true },
        first_name: { type: String, required: true },
        last_name: { type: String, required: true },
        amount: { type: Number, required: true },
        onComplete: { type: Function, required: true }
      },
      data() {
        return {
          loading: false,
          vendor: null,
          transactionId: null
        };
      },
      methods: {
        async fetchVendorDetails() {
          this.loading = true;
          try {
            const response = await fetch(`https://nairafy.ng/api/escrows/vendors/${this.vendorId}`, {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                email: this.email,
                phone_number: this.phone_number,
                first_name: this.first_name,
                last_name: this.last_name,
                amount: this.amount
              })
            });
            const data = await response.json();
            this.vendor = data.vendor;
            this.transactionId = data.transaction_id;
            this.showPaymentOptions();
          } catch (err) {
            alert('Error fetching vendor details.');
          } finally {
            this.loading = false;
          }
        },

        showPaymentOptions() {
          const modal = document.createElement('div');
          modal.innerHTML = `
            <div class="nairafy-modal">
              <img src="${this.vendor.logo}" alt="Vendor Logo" style="height: 50px" />
              <h3>Pay ${this.vendor.name}</h3>
              <button id="alatpay-button">Pay with AlatPay</button>
              <button id="quickteller-button">Pay with QuickTeller</button>
            </div>`;
          document.body.appendChild(modal);

          document.getElementById('alatpay-button').onclick = this.payWithAlatPay;
          document.getElementById('quickteller-button').onclick = () => alert('QuickTeller - Coming Soon');
        },

        async payWithAlatPay() {
          await this.loadAlatPayScript();
          const popup = window.Alatpay.setup({
            apiKey: '', // will be embedded server-side
            businessId: this.vendor.alatpay_business_id,
            email: this.email,
            phone: this.phone_number,
            firstName: this.first_name,
            lastName: this.last_name,
            currency: 'NGN',
            amount: this.amount,
            onTransaction: async (response) => {
              await this.updateTransaction('success', response);
            },
            onClose: async () => {
              await this.updateTransaction('failed', { message: 'User closed payment' });
            }
          });
          popup.show();
        },

        async updateTransaction(status, paymentDetails) {
          try {
            const response = await fetch(`https://nairafy.ng/api/escrows/transaction/${this.transactionId}`, {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                status,
                paymentDetails
              })
            });
            const result = await response.json();
            this.onComplete(result);
          } catch (err) {
            alert('Failed to update transaction.');
          }
        },

        loadAlatPayScript() {
          return new Promise((resolve) => {
            if (document.getElementById('alatpay-script')) return resolve();
            const script = document.createElement('script');
            script.src = 'https://web.alatpay.ng/js/alatpay.js';
            script.id = 'alatpay-script';
            script.onload = resolve;
            document.head.appendChild(script);
          });
        }
      },
      render() {
        return h(
          'button',
          {
            disabled: this.loading,
            onClick: this.fetchVendorDetails
          },
          this.loading ? 'Processing...' : 'Pay with Nairafy'
        );
      }
    });
  }
};
