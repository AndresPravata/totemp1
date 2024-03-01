import { FaArrowLeftLong } from "react-icons/fa6";
import { Button } from "./ui/button";
import { useNavigate } from "react-router";

const NavBar = () => {
  const navigate = useNavigate();
  const handleBack = () => {
    navigate("/totem");
  };
  return (
    <nav className="bg-transparent w-full ">
      <div className="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <div className="flex gap-3 justify-center items-center">
          <img src="logo.svg" className="h-14 w-14" alt="Flowbite Logo" />
          <span
            className="text-md sm:text-2xl font-bold whitespace-nowrap text-white uppercase"
            style={{ WebkitTextStroke: "1.5px black" }}
          >
            Veterinaria Dr.Luffi
          </span>
        </div>
        <div className=" bg-gray-500 rounded-md bg-clip-padding backdrop-filter backdrop-blur-lg bg-opacity-0 border border-gray-100">
          <div className="relative group">
            <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500 animate-tilt "></div>
            <Button
              className="relative px-4 py-4 bg-transparent rounded-lg leading-none flex items-center divide-x divide-gray-600 text-2xl uppercase hover:bg-black"
              size={"sm"}
              onClick={handleBack}
            >
              <FaArrowLeftLong />
            </Button>
          </div>
        </div>
      </div>
    </nav>
  );
};

export default NavBar;
