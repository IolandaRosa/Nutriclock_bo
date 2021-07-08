<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Avaliação Nutriclock
                    </h3>
                    <div class="component-wrapper-right"/>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <EvaluationItem :question="'1. Considero a app Nutriclock fácil de usar?'" :value="question1" :answer="answer1" />
                    <EvaluationItem :question="'2. Tive de aprender muito de forma a conseguir usar a app Nutriclock?'" :value="question2" :answer="answer2"/>
                    <EvaluationItem :question="'3.Fui capaz de registar a informação necessária através da app Nutriclock?'" :value="question3" :answer="answer3" />
                    <EvaluationItem :question="'4. Achei que a app Nutriclock não tinha inconsistências?'" :value="question4" :answer="answer4"/>
                    <EvaluationItem :question="'5. A organização das informações/menus da app Nutriclock foi clara?'" :value="question5" :answer="answer5" />
                    <EvaluationItem :question="'6. De um modo geral, estou satisfeito/a com a app Nutriclock?'" :value="question6" :answer="answer6" />
                    <EvaluationItem :question="'7. Considero que que houve profissionais disponíveis para me ajudar quando tive dificuldades ao\n'+
'                            usar app Nutriclock?'" :value="question7" :answer="answer7" />
                    <EvaluationItem :question="'8. As instruções de utilização da app Nutriclock são claras e detalhadas?'" :value="question8" :answer="answer8" />
                    <EvaluationItem :question="'9. Em geral, penso que as aplicações de saúde podem ser úteis?'" :value="question9" :answer="answer9" />
                    <EvaluationItem :question="'10. Tenciono continuar a usar aplicações de saúde no futuro?'" :value="question10" :answer="answer10" />
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */

import { ROUTE } from '../../utils/routes';
import EvaluationItem from '../evaluation/EvaluationItem';

export default {
    data() {
        return {
            isFetching: false,
            question1: 0,
            question2: 0,
            question3: 0,
            question4: 0,
            question5: 0,
            question6: 0,
            question7: 0,
            question8: 0,
            question9: 0,
            question10: 0,
            answer1: 0,
            answer2: 0,
            answer3: 0,
            answer4: 0,
            answer5: 0,
            answer6: 0,
            answer7: 0,
            answer8: 0,
            answer9: 0,
            answer10: 0,
        };
    },
    methods: {
        getAverageEvaluation() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/average-evaluation').then(response => {
                this.isFetching = false;
                if (response.data) {
                    this.question1 = Number(response.data.question1).toFixed(1)
                    this.question2 = Number(response.data.question2).toFixed(1)
                    this.question3 = Number(response.data.question3).toFixed(1)
                    this.question4 = Number(response.data.question4).toFixed(1)
                    this.question5 = Number(response.data.question5).toFixed(1)
                    this.question6 = Number(response.data.question6).toFixed(1)
                    this.question7 = Number(response.data.question7).toFixed(1)
                    this.question8 = Number(response.data.question8).toFixed(1)
                    this.question9 = Number(response.data.question9).toFixed(1)
                    this.question10 = Number(response.data.question10).toFixed(1)
                    this.answer1 = Number(response.data.answer1)
                    this.answer2 = Number(response.data.answer2)
                    this.answer3 = Number(response.data.answer3)
                    this.answer4 = Number(response.data.answer4)
                    this.answer5 = Number(response.data.answer5)
                    this.answer6 = Number(response.data.answer6)
                    this.answer7 = Number(response.data.answer7)
                    this.answer8 = Number(response.data.answer8)
                    this.answer9 = Number(response.data.answer9)
                    this.answer10 = Number(response.data.answer10)
                }
            }).catch(error => {
                console.log(error);
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            });
        },
    },
    async mounted() {
        this.getAverageEvaluation();
    },
    components: {
        EvaluationItem,
    }
};
</script>
