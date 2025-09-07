<template>
<div class="demo-container">
    <textarea v-model="formula" cols="30" rows="10"></textarea>
    <math-jax :formula="formula"></math-jax>
    <math-jax latex="\frac{x^2}{a^2}-\frac{y^2}{b^2}=1"></math-jax>
</div>
</template>
<script>
export default {
  name: 'HelloWorld',
  data () {
    return {
      formula: '\\frac{1}{2}(2^{20} + \\binom{20}{10})'
    }
  }
}
</script>
<style>
.demo-container {
  text-align: center;
}
</style>
