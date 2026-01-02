import { ref } from 'vue';

export function useHangmanCanvas() {
    const canvasRef = ref(null);
    let ctx = null;

    const initContext = () => {
        if (canvasRef.value) {
            ctx = canvasRef.value.getContext('2d');
        }
    };

    const clear = () => ctx?.clearRect(0, 0, 300, 300);

    const drawStand = () => {
        if (!ctx) return;
        ctx.beginPath();
        ctx.moveTo(60, 300);
        ctx.lineTo(60, 2);
        ctx.lineTo(170, 2);
        ctx.lineTo(170, 25);
        ctx.lineWidth = 4;
        ctx.strokeStyle = '#607D8B';
        ctx.stroke();
    };

    const bodyParts = [
        () => { // drawHead
            ctx.beginPath();
            ctx.arc(170, 60, 35, 0, 2 * Math.PI);
            ctx.moveTo(155, 50);
            ctx.arc(155, 50, 2, 0, 2 * Math.PI);
            ctx.moveTo(185, 50);
            ctx.arc(185, 50, 2, 0, 2 * Math.PI);
            ctx.moveTo(160, 75);
            ctx.lineTo(180, 75);
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#C51109';
            ctx.stroke();
        },
        () => { // drawSpine
            ctx.beginPath();
            ctx.moveTo(170, 95);
            ctx.lineTo(170, 200);
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#C51109';
            ctx.stroke();
        },
        () => { // drawLeftHand
            ctx.beginPath();
            ctx.moveTo(170, 135);
            ctx.lineTo(120, 105);
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#C51109';
            ctx.stroke();
        },
        () => { // drawRightHand
            ctx.beginPath();
            ctx.moveTo(170, 135);
            ctx.lineTo(220, 105);
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#C51109';
            ctx.stroke();
        },
        () => { // drawLeftLeg
            ctx.beginPath();
            ctx.moveTo(170, 200);
            ctx.lineTo(220, 230);
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#C51109';
            ctx.stroke();
        },
        () => { // drawRightLeg
            ctx.beginPath();
            ctx.moveTo(170, 200);
            ctx.lineTo(120, 230);
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#C51109';
            ctx.stroke();
        }
    ];

    const updateVisuals = (triesLeft) => {
        const stage = 6 - triesLeft;
        if (stage > 0 && bodyParts[stage - 1]) {
            bodyParts[stage - 1]();
        }
    };

    return { canvasRef, initContext, clear, drawStand, updateVisuals };
}
