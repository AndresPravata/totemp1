const Visor = () => {
  return (
    <div className="flex h-screen ">
      <div className="w-[20%] bg-gray-800 text-white p-4 overflow-hidden bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-zinc-950 to-black text-center gap-12 flex flex-col">
        <div className="flex flex-col mt-10">
          <h2 className="text-4xl mb-2 font-semibold uppercase ">
            Veterinaria
          </h2>
          <div className="rounded-2xl border-2 text-3xl border-white p-2 my-2 font-normal">
            A1 BOX1
          </div>
          <div className="rounded-2xl border-2 text-3xl border-white p-2 my-2">
            A3 BOX3
          </div>
        </div>
        <div className="flex flex-col">
          <h2 className="text-4xl mb-2 font-semibold uppercase">Ventas</h2>
          <div className="rounded-2xl border-2 text-3xl border-white p-2 my-2">
            C1
          </div>
        </div>
        <div className="flex flex-col">
          <h2 className="text-4xl mb-2 font-semibold uppercase">Peluquería</h2>
          <div className="rounded-2xl border-2 text-3xl border-white p-2 my-2">
            P1 10:30
          </div>
        </div>
      </div>
      <div className="w-[80%] bg-black">
        <video
          className="h-full w-full object-cover"
          muted
          loop
          autoPlay
          src="/visor-video.mp4"
        ></video>
      </div>
    </div>
  );
};

export default Visor;
