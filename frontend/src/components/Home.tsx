import { Button } from "./ui/button";
import { useNavigate } from "react-router-dom";

const Home = () => {
  const navigate = useNavigate();
  const handleTotem = () => {
    navigate("/totem");
  };

  const handleBox1 = () => {
    navigate("/box1");
  };
  const handleBox2 = () => {
    navigate("/box2");
  };
  const handleVisor = () => {
    navigate("/visor");
  };

  return (
    <section className="overflow-hidden bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-zinc-950 to-black w-full flex items-center mx-auto flex-col h-screen sm:px-16 px-6">
      <div className="flex flex-col gap-20">
        <div className="flex justify-center items-center gap-12 mt-6">
          <img
            src="logo.svg"
            alt="logo"
            className="rounded-full w-24 h-24 object-cover"
          />
          <h1 className=" text-white lg:text-[50px] sm:text-[40px] xs:text-[30px] text-[35px] font-bold uppercase text-center">
            Veterinaria Dr.Luffi
          </h1>
        </div>

        <div className="flex items-center justify-center gap-10 flex-col md:flex-row">
          <div className="grid gap-8 items-start justify-center">
            <div className="relative group">
              <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
              <Button
                className="relative px-7 py-4 bg-black rounded-lg leading-none flex items-center divide-x divide-gray-600 text-2xl uppercase w-52 h-15 hover:bg-black"
                size={"sm"}
                onClick={handleTotem}
              >
                Totem
              </Button>
            </div>
          </div>
          <div className="grid gap-8 items-start justify-center">
            <div className="relative group">
              <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
              <Button
                className="relative px-7 py-4 bg-black rounded-lg leading-none flex items-center divide-x divide-gray-600 text-2xl uppercase w-42 h-15 hover:bg-black"
                size={"sm"}
                onClick={handleBox1}
              >
                Box1
              </Button>
            </div>
          </div>
          <div className="grid gap-8 items-start justify-center">
            <div className="relative group">
              <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
              <Button
                className="relative px-7 py-4 bg-black rounded-lg leading-none flex items-center divide-x divide-gray-600 text-2xl uppercase w-42 h-15 hover:bg-black"
                size={"sm"}
                onClick={handleBox2}
              >
                Box2
              </Button>
            </div>
          </div>
          <div className="grid gap-8 items-start justify-center">
            <div className="relative group">
              <div className="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-sky-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-500 group-hover:duration-500  animate-tilt"></div>
              <Button
                className="relative px-7 py-4 bg-black rounded-lg leading-none flex items-center divide-x divide-gray-600 text-2xl uppercase w-42 h-15 hover:bg-black"
                size={"sm"}
                onClick={handleVisor}
              >
                Visor
              </Button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Home;
