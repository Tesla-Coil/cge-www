uses SysUtils, CastleVectors,
  CastleFilesUtils, CastleWindow, CastleSceneCore, CastleScene;

var
  Window: TCastleWindow;
  CarScene, RoadScene: TCastleScene;

procedure WindowUpdate(Container: TUIContainer);
var
  T: TVector3;
begin
  T := CarScene.Translation;
  { Thanks to multiplying by SecondsPassed, it is a time-based operation,
    and will always move 40 units / per second along the -Z axis. }
  T := T + Vector3(0, 0, -40) * Container.Fps.SecondsPassed;
  { Wrap the Z position, to move in a loop }
  if T.Z < -70.0 then
    T.Z := 50.0;
  CarScene.Translation := T;
end;

begin
  Window := TCastleWindow.Create(Application);
  Window.OnUpdate := @WindowUpdate;
  Window.Open;

  CarScene := TCastleScene.Create(Application);
  CarScene.Load(ApplicationData('car.x3d'));
  CarScene.Spatial := [ssRendering, ssDynamicCollisions];
  CarScene.ProcessEvents := true;

  RoadScene := TCastleScene.Create(Application);
  RoadScene.Load(ApplicationData('road.x3d'));
  RoadScene.Spatial := [ssRendering, ssDynamicCollisions];
  RoadScene.ProcessEvents := true;

  Window.SceneManager.Items.Add(CarScene);
  Window.SceneManager.Items.Add(RoadScene);
  Window.SceneManager.MainScene := RoadScene;

  // nice camera to see the road
  Window.SceneManager.RequiredCamera.SetView(
    Vector3(-43.30, 27.23, -80.74),
    Vector3(  0.60, -0.36,   0.70),
    Vector3(  0.18,  0.92,   0.32)
  );

  Application.Run;
end.
