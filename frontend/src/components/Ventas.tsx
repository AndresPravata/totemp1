import { useNavigate } from "react-router";
import NavBar from "./NavBar";
import { Button } from "./ui/button";
import { useState, useEffect } from "react";
import io from "socket.io-client";
import axios from "axios";
import { HOST, SOCKET } from "@/lib/utils";
import bgImage from "../assets/bg.jpg";

const Ventas = () => {
  const socket = io(`${SOCKET}`);
  const navigate = useNavigate();
  const [cantidadState, setCantidadState] = useState<number>(0);
  const [isButtonDisabled, setButtonDisabled] = useState(false);

  const postData = async (box: number, nombre_turno: string) => {
    try {
      if (localStorage.getItem("turnoBox4") == "1") {
        await axios.post(`${HOST}/turnos/`, {
          nombre_turno: nombre_turno,
          numero_box: box,
          estado: "Actual"
        });
      } else {
        await axios.post(`${HOST}/turnos/`, {
          nombre_turno: nombre_turno,
          numero_box: box,
          estado: "Ventas"
        });
      }
    } catch (error) {
      console.error("Error al obtener los turnos", error);
    }
  };

  const fetchData = async () => {
    try {
      const box4Answer = await axios.get(`${HOST}/turnos/cantidadTurnos/4`);

      setCantidadState(box4Answer?.data);
    } catch (error) {
      console.error("Error al obtener los turnos", error);
    }
  };

  useEffect(() => {
    if (new Date().getHours() === 7) {
      localStorage.clear();
    }

    fetchData();
  }, []);

  const handleImprimirTurno = async () => {
    setButtonDisabled(true);
    localStorage.setItem(
      `turnoBox4`,
      String(Number(localStorage.getItem(`turnoBox4`) ?? "0") + 1)
    );

    await postData(4, `C${localStorage.getItem(`turnoBox4`) ?? "0"} BOX4`);
    const box = `C`;
    socket.emit("actualizarBox", { box });
    socket.emit("actualizarTurnos");

    navigate("/totem");
    setButtonDisabled(false);
  };

  return (
    <section
      className=" bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-zinc-950 to-black w-full flex items-center mx-auto flex-col min-h-[100vh]"
      style={{
        backgroundImage: `url(${bgImage})`,
        backgroundRepeat: "no-repeat",
        backgroundSize: "cover",
      }}
    >
      <article className=" w-full min-h-[100vh]">
        <NavBar />
        <div className="flex flex-col gap-20 mt-8">
          <div className="flex items-center justify-center gap-6 flex-col">
            <h1
              className=" uppercase font-bold text-2xl text-white"
              style={{ WebkitTextStroke: "1.5px black" }}
            >
              Turnos para venta
            </h1>
            <div className="grid gap-3 items-start justify-center">
              <div className="relative group">
                <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
                <img
                  src="ventas.png"
                  alt="ventas"
                  className={`rounded-lg relative bg-black leading-none flex items-center divide-x divide-gray-600 uppercase px-0 xs:w-[30rem] w-72 h-15 cursor-pointer`}
                />
              </div>
              {/* GET de los turnos que hay en espera */}
              <p
                className=" uppercase font-bold text-2xl text-white"
                style={{ WebkitTextStroke: "1.5px black" }}
              >
                Turnos en espera: {cantidadState}
              </p>
            </div>
            <div className="grid gap-3 items-start justify-center">
              <div className=" bg-gray-400 rounded-md bg-clip-padding backdrop-filter backdrop-blur-lg bg-opacity-10 border border-gray-100 mb-10">
                <div className="relative group">
                  <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
                  <Button
                    className="relative bg-transparent rounded-lg leading-none flex items-center divide-x divide-gray-600 uppercase text-2xl px-2 hover:bg-black"
                    size={"sm"}
                    type="submit"
                    onClick={handleImprimirTurno}
                    disabled={isButtonDisabled} // Use the state here
                  >
                    Imprimir Turno
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </article>
    </section>
  );
};

export default Ventas;
