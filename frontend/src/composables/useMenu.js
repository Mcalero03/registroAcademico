import { storeToRefs } from "pinia";
import { useMenuStore } from "../stores/menu";

const useMenu = () => {
  const menuStore = useMenuStore();
  const { stateSideBar } = storeToRefs(menuStore);

  return {
    stateSideBar,
  };
};

export default useMenu;
