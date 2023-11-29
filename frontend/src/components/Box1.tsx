import { useEffect, useState } from "react";
import { io, Socket } from "socket.io-client";
import axios from "axios";

export interface Turno {
  id: number;
  nombre_turno: string | null;
  createdAt: Date;
  fecha_hora_inicio: Date;
  fecha_hora_fin: Date;
  estado: string;
}

interface TurnoState {
  actual: Turno | null;
  siguiente: Turno | null;
  espera: Turno | null;
}

const Box1 = () => {
  const [socket, setSocket] = useState<Socket | null>(null);
  const [isPresent, setIsPresent] = useState(false);
  const [turnoState, setTurnoState] = useState<TurnoState>({
    actual: null,
    siguiente: null,
    espera: null,
  });

  const fetchData = async () => {
    try {
      const response = await axios.get(`http://127.0.0.1:5000/turnos/turnosBox/BOX1`);
      setTurnoState({
        actual: response.data[0],
        siguiente: response.data[1],
        espera: response.data[2],
      });
    } catch (error) {
      console.error("Error al obtener los turnos", error);
    }
  };

  const start = async () => {
    try {
      const response = await axios.put(
        `http://127.0.0.1:5000/turnos/${
          turnoState.actual == null ? 0 : turnoState.actual.id
        }`,
        {
          fecha_hora_inicio: new Date(),
          estado: "Iniciado",
        }
      );
      setTurnoState({ ...turnoState, actual: response.data });
    } catch (error) {
      console.error("Error al actualizar el turno", error);
    }
  };

  const end = async () => {
    try {
      const response = await axios.put(
        `http://127.0.0.1:5000/turnos/${
          turnoState.actual == null ? 0 : turnoState.actual.id
        }`,
        {
          fecha_hora_fin: new Date(),
          estado: "Finalizado",
        }
      );
      setTurnoState({ ...turnoState, actual: response.data });
    } catch (error) {
      console.error("Error al actualizar el turno", error);
    }
  };

  useEffect(() => {
    // Al cargar el componente, verifica si hay un estado guardado en LocalStorage
    const savedState = localStorage.getItem("veterinarioPresente");
    if (savedState) {
      setIsPresent(savedState === "true");
    }

    fetchData();
  }, []);

  useEffect(() => {
    // Conectar al servidor WebSocket al cargar el componente
    const socket = io("http://localhost:5000");

    socket.on("estadoVeterinario", (estado) => {
      setIsPresent(estado === "presente");
      // Guardar el estado en LocalStorage
      localStorage.setItem("veterinarioPresente", estado);
    });

    setSocket(socket);

    return () => {
      // Desconectar al desmontar el componente
      socket.disconnect();
    };
  }, []);

  const handlePresentClick = () => {
    setIsPresent(true);
    // Enviar un evento al servidor para indicar que el veterinario está presente
    socket?.emit("veterinarioPresente");
    localStorage.setItem("veterinarioPresente", "true");
  };

  const handleAbsentClick = () => {
    setIsPresent(false);
    // Enviar un evento al servidor para indicar que el veterinario está ausente
    socket?.emit("veterinarioAusente");
    localStorage.setItem("veterinarioPresente", "false");
  };

  return (
    <section className="overflow-hidden bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-zinc-950 to-black w-full flex items-center mx-auto flex-col h-screen sm:px-16 px-6">
      <div className="flex flex-col gap-20">
        <div className="flex justify-center items-center flex-col gap-5 mt-6">
          <h1 className=" text-white lg:text-[60px] sm:text-[50px] xs:text-[40px] text-[40px] font-bold uppercase text-center">
            Box 1
          </h1>
          <div className=" justify-center items-center flex flex-col gap-5">
            <p className=" text-white text-lg">Presencia de veterinario</p>
            <div className="flex gap-6">
              <button
                onClick={handlePresentClick}
                style={{ backgroundColor: isPresent ? "green" : "grey" }}
                className="p-3 rounded-lg text-slate-950 font-medium uppercase"
              >
                Presente
              </button>
              <button
                onClick={handleAbsentClick}
                style={{ backgroundColor: !isPresent ? "red" : "grey" }}
                className="p-3 rounded-lg text-slate-950 font-medium uppercase"
              >
                Ausente
              </button>
            </div>
            <div className="table-container rounded-lg border-2 border-white mt-5 overflow-hidden">
              <table className="text-white text-lg divide-white divide-y-2 w-full">
                <thead>
                  <tr>
                    <th className="px-4 py-2 border-r-2 border-white font-normal">
                      Turno actual
                    </th>
                    <th className="px-4 py-2 border-r-2 border-white font-normal">
                      Turno siguiente
                    </th>
                    <th className="px-4 py-2 font-normal">Espera</th>
                  </tr>
                </thead>
                <tbody>
                  {/* Aca hay que reemplazar por las instancias en tiempo real de los turnos */}
                  <tr>
                    <td className="px-4 py-2 border-r-2 border-white text-center font-bold">
                      {turnoState.actual?.nombre_turno ?? "NULL"}
                    </td>
                    <td className="px-4 py-2 border-r-2 border-white text-center font-bold">
                      {turnoState.siguiente?.nombre_turno ?? "NULL"}
                    </td>
                    <td className="px-4 py-2 text-center font-bold">
                      {turnoState.espera?.nombre_turno ?? "NULL"}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div className="flex gap-6 mt-6">
              <button
                className="p-3 rounded-lg text-slate-950 font-medium uppercase bg-blue-500"
                onClick={() => start()}
              >
                {/* UPDATE del estado del turno a iniciado */}
                Iniciar turno
              </button>
              <button
                className="p-3 rounded-lg text-slate-950 font-medium uppercase bg-yellow-300"
                onClick={() => end()}
              >
                {/* UPDATE del estado del turno a finalizado */}
                Finalizar turno
              </button>
              <button
                className="p-3 rounded-lg text-slate-950 font-medium uppercase bg-green-400"
                onClick={() => fetchData()}
              >
                {/* Acá no se bien que sería pero es para que el veterinario pase al siguiente turno. Creo que se deberia finalizar el turno que esta iniciado. */}
                Siguiente
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Box1;
