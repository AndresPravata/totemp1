import { useEffect, useState } from "react";
import axios from "axios";
import { Turno } from "./Box1";
import { HOST, SOCKET } from "@/lib/utils";
import { io } from "socket.io-client";

interface TurnoState {
  Box1: Turno | null;
  Box2: Turno | null;
  Ventas: Turno | null;
}

const Visor = () => {
  const [turnoState, setTurnoState] = useState<TurnoState>({
    Box1: null,
    Box2: null,
    Ventas: null,
  });

  const fetchData = async () => {
    try {
      const response = await axios.get(`${HOST}/turnos/turnosVisor`);
      setTurnoState(response.data);
    } catch (error) {
      console.error("Error al obtener los turnos", error);
    }
  };

  useEffect(() => {
    const socket = io(`${SOCKET}`);

    socket.on("connect", () => {
      console.log("Conexión Socket.IO establecida con éxito");
    });

    socket.on("consultarTurnos", (turno) => {
      console.log("turnos recibidos correctamente");
      setTurnoState(turno);
    });

    socket.on("disconnect", () => {
      console.log("Desconexión Socket.IO");
    });

    return () => {
      // Desconectar al desmontar el componente
      socket.disconnect();
    };
  }, []);

  useEffect(() => {
    fetchData();
  }, []);

  return (
    <div className="flex h-screen">
      <div className="w-[20%] bg-gray-800 text-white p-4 overflow-hidden bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-zinc-950 to-black text-center gap-12 flex flex-col justify-center">
        <div className="flex flex-col gap-9 ml-10">
          <div className="flex flex-col">
            <h2 className="text-4xl mb-2 font-semibold uppercase ">
              Veterinaria
            </h2>
            <div className="rounded-2xl border-2 text-3xl border-white p-6 my-2 font-normal">
              {/* GET del turno al que da inicio el veterinario 1 */}
              {turnoState.Box1?.nombre_turno ?? ""}
            </div>
            <div className="rounded-2xl border-2 text-3xl border-white p-6 my-2">
              {/* GET del turno al que da inicio el veterinario 2*/}
              {turnoState.Box2?.nombre_turno ?? ""}
            </div>
          </div>
          <div className="flex flex-col">
            <h2 className="text-4xl mb-2 font-semibold uppercase">Ventas</h2>
            <div className="rounded-2xl border-2 text-3xl border-white p-6 my-2">
              {/* GET del turno de ventas */}
              {turnoState.Ventas?.nombre_turno?.substring(0, 2) ?? ""}
            </div>
          </div>
        </div>

        {/* <div className="flex flex-col">
          <h2 className="text-4xl mb-2 font-semibold uppercase">Peluquería</h2>
          <div className="rounded-2xl border-2 text-3xl border-white p-2 my-2">
            P1 10:30
          </div>
        </div> */}
      </div>
      <div className="w-[80%] g-gray-800 text-white p-4 overflow-hidden bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-slate-900 via-zinc-950 to-black text-center">
        <video
          className="h-full w-screen"
          loop
          autoPlay
          src="/visor-video.mp4"
        ></video>
      </div>
    </div>
  );
};

export default Visor;
