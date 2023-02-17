<div class="tablero" id="tablero-container">
    <div class="tablero-card brillos">
        <span>Melarc</span>
    </div>

    <div class="tablero-card">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
        <span>Melarc</span>
    </div>
</div>


<style>
    .tablero {

        background: linear-gradient(to bottom right, #409366, #1eae5f);
        width: 80%;
        height: 500px;
        border-radius: 10px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        flex-wrap: wrap;
        padding: 20px;

    }

    .tablero-card {
        width: 125px;
        height: 200px;
        background: #fff;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 2rem;
        font-weight: bold;
        background-image: url("{{ asset('img/card.jpg') }}");
        background-size: cover;
        background-position: center;
        margin: 20px;
        padding: 10px;
    }

    .brillos {
        box-shadow: 0px 0px 13px 13px #fff;
        animation: brillo 2s ease-in-out infinite;

    }

    @keyframes brillo {
        0% {
            box-shadow: 0px 0px 40px rgba(255, 255, 255, 0.9);
        }

        50% {
            box-shadow: 0px 0px 50px rgba(255, 255, 255, 0.9), 0px 0px 30px rgba(255, 255, 255, 0.9);
        }

        100% {
            box-shadow: 0px 0px 30px rgba(255, 255, 255, 0.9);
        }
    }
</style>