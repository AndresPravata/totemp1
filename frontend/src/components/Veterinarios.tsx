import { useNavigate } from "react-router";
import NavBar from "./NavBar";
import { Button } from "./ui/button";
import { useState, useEffect } from "react";
import io from "socket.io-client";
import {
  useEstadoVeterinario,
  useEstadoVeterinario2,
} from "../hooks/useEstadoVeterinario";
import axios from "axios";

interface cantidadState {
  box1: number;
  box2: number;
}

interface veterinarioSelectedState {
  veterinario: number;
  box: number;
}

const Veterinarios = () => {
  const navigate = useNavigate();
  const [veterinarioSelected, setVeterinarioSelected] =
    useState<veterinarioSelectedState>({ veterinario: 1, box: 1 });
  const [cantidadState, setCantidadState] = useState<cantidadState>({
    box1: 0,
    box2: 0,
  });

  const estadoVeterinario = useEstadoVeterinario();
  const estadoVeterinario2 = useEstadoVeterinario2();

  const postData = async (id: number, box: number, nombre_turno: string) => {
    try {
      if (
        (localStorage.getItem("turnoBox1") === "1" &&
          veterinarioSelected.veterinario === 1) ||
        (localStorage.getItem("turnoBox2") === "1" &&
          veterinarioSelected.veterinario === 2)
      ) {
        const response = await axios.post(`http://127.0.0.1:5000/turnos/`, {
          nombre_turno: nombre_turno,
          numero_box: box,
          veterinario_id: id,
          estado: "Actual",
        });
      } else {
        const response = await axios.post(`http://127.0.0.1:5000/turnos/`, {
          nombre_turno: nombre_turno,
          numero_box: box,
          veterinario_id: id,
        });
      }
    } catch (error) {
      console.error("Error al obtener los turnos", error);
    }
  };

  const fetchData = async () => {
    try {
      const box2Answer = await axios.get(
        `http://127.0.0.1:5000/turnos/cantidadTurnos/2`
      );
      const box1Answer = await axios.get(
        `http://127.0.0.1:5000/turnos/cantidadTurnos/1`
      );

      setCantidadState({
        box1: box1Answer.data,
        box2: box2Answer.data,
      });
    } catch (error) {
      console.error("Error al obtener los turnos", error);
    }
  };

  useEffect(() => {
    if (new Date().getHours() === 7) {
      localStorage.clear();
    }

    fetchData();
    const socket = io("http://localhost:5000");
    return () => {
      socket.disconnect();
    };
  }, []);

  const handleVeterinario1 = () => {
    setVeterinarioSelected({ veterinario: 1, box: 1 });
    localStorage.setItem("veterinario", "1"); // Guarda el estado en el LocalStorage
  };
  const handleVeterinario2 = () => {
    setVeterinarioSelected({ veterinario: 2, box: 2 });
    localStorage.setItem("veterinario", "2"); // Guarda el estado en el LocalStorage
  };

  const handleImprimirTurno = () => {
    localStorage.setItem(
      `turnoBox${veterinarioSelected.box}`,
      String(
        Number(
          localStorage.getItem(`turnoBox${veterinarioSelected.box}`) ?? "0"
        ) + 1
      )
    );
    postData(
      veterinarioSelected.veterinario,
      veterinarioSelected.box,
      `A${
        localStorage.getItem(`turnoBox${veterinarioSelected.box}`) ?? "0"
      } BOX${veterinarioSelected.box}`
    );
    navigate("/totem");
  };
  return (
    <section className=" bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-zinc-950 to-black w-full flex items-center mx-auto flex-col min-h-[100vh]">
      <article className=" w-full min-h-[100vh]">
        <NavBar />
        <div className="flex flex-col gap-20 mt-8">
          <div className="flex items-center justify-center gap-6 flex-col">
            <div className="grid gap-3 items-start justify-center">
              <div className="relative group">
                <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
                <img
                  src="veterinario1.png"
                  alt="veterinario 1"
                  onClick={
                    estadoVeterinario === "presente"
                      ? handleVeterinario1
                      : undefined
                  }
                  className={`rounded-lg relative bg-black leading-none flex items-center divide-x divide-gray-600 uppercase px-0 xs:w-[30rem] w-72 h-15 cursor-pointer ${
                    estadoVeterinario === "ausente"
                      ? "grayscale pointer-events-none"
                      : ""
                  }`}
                  style={
                    veterinarioSelected.veterinario === 1
                      ? {
                          border: "3px solid black",
                        }
                      : { border: "none" }
                  }
                />
              </div>
              {/* GET de los turnos que hay en espera */}
              <p className=" text-white">
                Turnos en espera: {cantidadState.box1}
              </p>
            </div>
            <div className="grid gap-3 items-start justify-center">
              <div className="relative group">
                <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500 animate-tilt"></div>
                <img
                  src="veterinario2.png"
                  alt="veterinario 2"
                  onClick={
                    estadoVeterinario2 === "presente"
                      ? handleVeterinario2
                      : undefined
                  }
                  className={`rounded-lg relative bg-black leading-none flex items-center divide-x divide-gray-600 uppercase px-0 xs:w-[30rem] w-72 h-15 cursor-pointer ${
                    estadoVeterinario2 === "ausente"
                      ? "grayscale pointer-events-none"
                      : ""
                  }`}
                  style={
                    veterinarioSelected.veterinario === 2
                      ? {
                          border: "3px solid black",
                        }
                      : { border: "none" }
                  }
                />
              </div>
              <p className="text-white mb-4 ">
                Turnos en espera: {cantidadState.box2}
              </p>
            </div>
            <div className="grid gap-3 items-start justify-center">
              <div className="relative group mb-10">
                <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
                <Button
                  className="relative bg-black rounded-lg leading-none flex items-center divide-x divide-gray-600 uppercase text-2xl px-2"
                  size={"sm"}
                  type="submit"
                  onClick={handleImprimirTurno}
                >
                  Imprimir Turno
                </Button>
              </div>
            </div>
          </div>
        </div>
      </article>
    </section>
  );
};

export default Veterinarios;
