<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
<table class="table table-head-fixed table-striped text-nowrap">
        <thead>
            <tr>
                <th>Date</th>
                <th v-if="source != 'company'">Company</th>
                <th>Reference</th>
                <th>Type</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody v-if="ledgers.length > 0">
            <tr v-for="(ledger, index) in ledgers" :key="ledger.id">
                <td>{{ ExcelDate(ledger.date) }}</td>
                <td v-if="source != 'company'">{{ ledger.company?.name }}</td>
                <td>
                    <router-link v-if="ledger.referenceable" :to="getReferenceLink(ledger)">{{ getReferenceLabel(ledger) }}</router-link>
                    <span v-else>-</span>
                </td>
                <td>{{ firstUp(ledger.type) }}</td>
                <td>{{ ledger.type == 'credit' ? currency(ledger.amount) : '-' }}</td>
                <td>{{ ledger.type == 'debit' ? currency(ledger.amount) : '-' }}</td>
                <td>{{ currency(ledger.balance) }}</td>
                
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Ledger entries meets your requirements</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            ledger: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    methods:{
        getReferenceType(referenceType) {
            if (!referenceType) return null;

            const map = {
                'App\\Models\\ConsultantPractice\\Payment': 'payment',
                'App\\Models\\ConsultantPractice\\Invoice': 'invoice',
                'App\\Models\\ConsultantPractice\\Session': 'invoice',
                'App\\Models\\Inventory\\PurchaseOrder': 'purchase_orders',
                'App\\Models\\Inventory\\SalesOrder': 'sales_orders',
            };

            return map[referenceType] || null;
        },
        getReferenceLink(ledger) {
            const referenceType = this.getReferenceType(
                ledger.reference_type
            );

            if (!referenceType) {
                return '#';
            }

            return `/consultant_practices/${this.type}/${referenceType}s/${ledger.reference_id}`;
        },
        getReferenceLabel(ledger) {
            const referenceType = this.getReferenceType(
                ledger.reference_type
            );

            if (!referenceType) {
                return 'Unknown Reference';
            }

            return `${this.firstUp(referenceType.replace('_', ' '))} ID: ${ledger.reference_id}`;
        }
    },
    mounted() {},
    props:{
        ledgers: {type: Array, default: () => []},
        source: {type: String, default: ''},
        type: {type: String, default: 'finance'}
    },
    watch:{}
}
</script>