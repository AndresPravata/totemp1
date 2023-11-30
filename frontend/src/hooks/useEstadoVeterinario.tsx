import { SOCKET } from "@/lib/utils";
import {
  createContext,
  useContext,
  useState,
  useEffect,
  ReactNode,
} from "react";
import io from "socket.io-client";

// Crea un nuevo contexto para el estado del veterinario
const VeterinarioContext = createContext("ausente");

// Define el tipo para las props de VeterinarioProvider
type VeterinarioProviderProps = {
  children: ReactNode;
};

// Crea un proveedor de contexto que mantenga el estado del veterinario
export const VeterinarioProvider = ({ children }: VeterinarioProviderProps) => {
  const [estadoVeterinario, setEstadoVeterinario] = useState("ausente");

  useEffect(() => {
    const socket = io(`${SOCKET}`);

    socket.on("estadoVeterinario", (estado) => {
      setEstadoVeterinario(estado);
    });

    return () => {
      socket.disconnect();
    };
  }, []);

  return (
    <VeterinarioContext.Provider value={estadoVeterinario}>
      {children}
    </VeterinarioContext.Provider>
  );
};

// Crea un hook personalizado para usar el estado del veterinario
export const useEstadoVeterinario = () => {
  return useContext(VeterinarioContext);
};

// Crea un nuevo contexto para el estado del segundo veterinario
const Veterinario2Context = createContext("ausente");

// Crea un proveedor de contexto que mantenga el estado del segundo veterinario
export const Veterinario2Provider = ({
  children,
}: VeterinarioProviderProps) => {
  const [estadoVeterinario2, setEstadoVeterinario2] = useState("ausente");

  useEffect(() => {
    const socket = io(`${SOCKET}`);

    socket.on("estadoVeterinario2", (estado) => {
      setEstadoVeterinario2(estado);
    });

    return () => {
      socket.disconnect();
    };
  }, []);

  return (
    <Veterinario2Context.Provider value={estadoVeterinario2}>
      {children}
    </Veterinario2Context.Provider>
  );
};

export const useEstadoVeterinario2 = () => {
  return useContext(Veterinario2Context);
};
