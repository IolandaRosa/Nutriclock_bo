<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Problema de Saúde</span>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="green-label">Seleciona a patologia</label>
                            <select
                                class="form-control"
                                v-model="selectedDisease">
                                <option v-for="d in diseasesList" :value="d.value">{{ d.label }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="green-label">Seleciona a alergia</label>
                            <select
                                class="form-control"
                                v-model="selectedAlergy">
                                <option v-for="d in allergiesList" :value="d.value">{{ d.label }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add-disease-modal-input" class="green-label">Outra</label>
                            <div>
                                <input
                                    id="add-disease-modal-input"
                                    type="text"
                                    class="form-control"
                                    v-model.trim="disease"
                                />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-bold btn btn-primary" @click="onSaveClick">
                                Adicionar
                            </button>
                            <button class="btn-bold btn btn-secondary" @click="onCloseClick">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
export default {
    props: ['allergiesList', 'diseasesList'],
    data() {
        return {
            selectedDisease: null,
            selectedAlergy: null,
            disease: null,
        };
    },
    methods: {
        onCloseClick() {
            this.$emit('close');
        },
        onSaveClick() {
            this.$emit('save', this.selectedDisease, this.selectedAlergy, this.disease);
            this.selectedDisease = null;
            this.selectedAlergy = null;
            this.disease = null;
        },
    }
};
</script>
