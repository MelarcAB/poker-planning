<div class="ticket-info bg-color-primary">
    <label id="ticketName">Ticket seleccionado: <span id="selectedTicket" style="font-weight: bold;">NOMBRE</span></label>
    <label id="ticketStatus">Estado: <span id="selectedTicketStatus" style="font-weight: bold;">POR VOTAR</span></label>
</div>
<div class="tablero" id="tablero-container">
    <div class="tablero-card brillos">
        <span>Melarc</span>
    </div>
    <div class="tablero-card">
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
        background-image: url("{{ asset($deck->image) }}");
        background-size: cover;
        background-position: center;
        margin: 20px;
        padding: 10px;
    }

    .brillos {
        box-shadow: 0px 0px 13px 13px #fff;
        animation: brillo 2s ease-in-out infinite;

    }

    /* Estilos para las etiquetas */
    .ticket-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
        width: 80%;
        box-sizing: border-box;
        flex-wrap: wrap;
    }

    .ticket-info label {
        font-size: 1.2rem;
        margin: 5px;
        flex: 1 0 100%;
        box-sizing: border-box;
        text-align: center;
        color: #fff;

    }

    @media (min-width: 576px) {
        .ticket-info label {
            flex: 1;
        }
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
