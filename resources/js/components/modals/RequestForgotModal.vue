<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" style="max-width: 400px">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Remoção de Conta</span>
                    </div>
                    <div class="modal-body m-0">
                        <p style="color: #000">Este utilizador tem um pedido de Esquecimento de Dados e eliminação dos dados da sua conta pendente. Por favor, escolha confirmar o Esquecimento para eliminar toda a informação de registos deste utilizador ou anular pedido para que o utilizador possa ficar ativo e prosseguir com o estudo.</p>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <button class="btn btn-sm btn-primary mb-1 w-100" @click="onAproveClick">
                            Confirmar Esquecimento de Dados
                        </button>
                        <button class="btn btn-sm btn-outline-primary mb-1 w-100" @click="onReproveClick">
                            Anular Esquecimento de Dados
                        </button>
                        <button class="btn btn-sm btn-secondary w-100" @click="onCloseClick">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
export default{
    data() {
        return {};
    },
    methods:{
        onCloseClick() {
            this.$emit('close');
        },
        onAproveClick() {
            this.$emit('aprove', true);
        },
        onReproveClick() {
            this.$emit('reprove', false);
        },
    },
};
</script>
